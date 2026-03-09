<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once JPATH_COMPONENT . '/helpers/image.php';

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Session\Session;
class JpagebuilderControllerMedia extends FormController {
	private function getMediaOwnerById($id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'created_by' )->from ( $db->quoteName ( '#__jpagebuilder_media' ) )->where ( $db->quoteName ( 'id' ) . ' = ' . $id );
		$db->setQuery ( $query );

		try {
			return $db->loadResult ();
		} catch ( \Exception $e ) {
			return 0;
		}
	}

	/**
	 *
	 * @since 2020
	 *        Create light weight image placeholder for lazy load feature
	 */
	private function create_media_placeholder($dest, $base_name, $ext) {
		$placeholder_folder_path = JPATH_ROOT . '/media/com_jpagebuilder/placeholder';

		if (! is_dir ( $placeholder_folder_path )) {
			Folder::create ( $placeholder_folder_path, 0755 );
		}

		$image = new JpagebuilderHelperImage ( $dest );
		list ( $srcWidth, $srcHeight ) = $image->getDimension ();
		$width = 60;
		$height = $width / ($srcWidth / $srcHeight);
		$image->createThumb ( array (
				'60',
				$height
		), $placeholder_folder_path, $base_name, $ext, 20 );
	}

	/**
	 *
	 * @since 2020
	 *        Delete placeholder image if exists
	 */
	private function delete_image_placeholder($file_path) {
		$filename = basename ( $file_path );
		$src = JPATH_ROOT . '/media/com_jpagebuilder/placeholder' . '/' . $filename;
		if (file_exists ( $src )) {
			File::delete ( $src );
		}
	}
	private static function in_array($needle, $haystack) {
		$it = new RecursiveIteratorIterator ( new RecursiveArrayIterator ( $haystack ) );

		foreach ( $it as $element ) {
			if ($element == $needle) {
				return true;
			}
		}

		return false;
	}
	private static function array_search($needle, $haystack) {
		foreach ( $haystack as $key => $value ) {
			$current_key = $key;

			if ($needle === $value or (is_array ( $value ) && self::array_search ( $needle, $value ) !== false)) {
				return $current_key;
			}
		}
		return false;
	}
	private function replacePathByTitle($path, $title) {
		$fileName = pathinfo ( $path, PATHINFO_FILENAME );
		$basename = basename ( $path );

		$newFile = str_replace ( $fileName, $title, $basename );

		return str_replace ( $basename, $newFile, $path );
	}

	/**
	 * Remove a media item.
	 *
	 * @param stdClass $item
	 *        	The media item object.
	 * @param object $model
	 *        	The media model.
	 * @param object $user
	 *        	The user class object.
	 *        	
	 * @return bool
	 * @since 4.0.0
	 */
	private function removeMediaItem($item, $model, $user): bool {
		$mediaType = $item ['type'];

		if ($mediaType === 'folder') {
			$path = Path::clean ( $item ['path'] );
			$folder = JPATH_ROOT . $path;

			try {
				$folder = BuilderMediaHelper::checkForMediaActionBoundary ( $folder );
			} catch ( \Exception $e ) {
				return false;
			}

			if (! JpagebuilderSecurityHelper::isActionableFolder ( $folder )) {
				return false;
			}

			if (is_dir ( $folder )) {
				Folder::delete ( $folder );

				return true;
			}

			return false;
		} elseif ($mediaType === 'local') {
			$src = JPATH_ROOT . '/' . $item ['path'];
			$src = Path::clean ( $src );

			try {
				BuilderMediaHelper::checkForMediaActionBoundary ( $src );
			} catch ( \Exception $e ) {
				return false;
			}

			if (\file_exists ( $src )) {
				$media = $model->getMediaByPath ( $item ['path'] );
				if (isset ( $media->thumb ) && $media->thumb) {
					if (file_exists ( JPATH_ROOT . '/' . $media->thumb )) {
						File::delete ( JPATH_ROOT . '/' . $media->thumb ); // Delete thumb
					}
				}

				// Delete placeholder too
				$this->delete_image_placeholder ( $item ['path'] );
				// Remove Path.
				$removeMediaByPath = $model->removeMediaByPath ( $item ['path'] );

				if (! File::delete ( $src ) || ! $removeMediaByPath) {
					return false;
				}

				return true;
			}

			return false;
		} elseif ($mediaType === 'local+db') {

			$media = $model->getMediaByID ( $item ['id'] );
			$src = $media->path ?? '';
			$src = JPATH_ROOT . '/' . $src;
			$src = Path::clean ( $src );
			$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder' ) || ($user->authorise ( 'core.edit.own', 'com_jpagebuilder' ) && ($media->created_by === $user->id));

			if (! $authorised) {
				return false;
			}

			try {
				BuilderMediaHelper::checkForMediaActionBoundary ( dirname ( $src ) );
			} catch ( \Exception $e ) {
				return false;
			}

			if (! $model->removeMediaByID ( $item ['id'] )) {
				return false;
			}

			if (! empty ( $media->thumb )) {
				$thumbSrc = $media->thumb ?? '';
				$thumbSrc = JPATH_ROOT . '/' . $thumbSrc;
				$thumbSrc = Path::clean ( $thumbSrc );

				try {
					BuilderMediaHelper::checkForMediaActionBoundary ( dirname ( $thumbSrc ) );
				} catch ( \Exception $e ) {
					return false;
				}

				if (\file_exists ( $thumbSrc )) {
					\unlink ( $thumbSrc );
				}
			}

			// Delete placeholder too
			$this->delete_image_placeholder ( $item ['path'] );

			if (\file_exists ( $src )) {
				\unlink ( $src );

				return true;
			}

			return false;
		}

		return false;
	}
	public function getModel($name = 'Media', $prefix = '', $config = array (
			'ignore_request' => true
	)) {
		return parent::getModel ( $name, $prefix, $config );
	}

	/**
	 * Rename the media file
	 *
	 * @return void
	 */
	public function renameMedia() {
		$app = Factory::getApplication ( 'site' );
		$input = $app->getInput();

		$id = $input->json->get ( 'id', 0, 'int' );
		$title = $input->json->get ( 'title', '', 'str' );
		$path = $input->json->get ( 'path', '', 'str' );
		$thumb = $input->json->get ( 'thumb', '', 'str' );

		$mediaType = empty ( $id ) ? 'folder' : 'DB';

		$data = new \stdClass ();
		$data->id = $id;
		$data->title = $title;
		$data->path = $this->replacePathByTitle ( $path, $title );
		$data->thumb = $this->replacePathByTitle ( $thumb, $title );
		$data->modified_on = Factory::getDate ()->toSql ();
		$data->modified_by = Factory::getApplication ()->getIdentity ()->id;

		$user = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise ( 'core.edit', 'com_jpagebuilder' ) || ($user->authorise ( 'core.edit.own', 'com_jpagebuilder' ) && $user->id === $this->getMediaOwnerById ( $id ));

		if (! $canEdit) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_MEDIA_RENAME" ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		$response = [ 
				'data' => Text::_ ( "COM_JPAGEBUILDER_MEDIA_MANAGER_MEDIA_RENAME_ERROR" ),
				'status' => false,
				'code' => 500
		];

		try {
			if ($mediaType === 'DB') {
				$db = Factory::getContainer()->get('DatabaseDriver');
				$db->updateObject ( '#__jpagebuilder_media', $data, 'id' );
			}

			\rename ( JPATH_ROOT . '/' . $path, JPATH_ROOT . '/' . $data->path );
			\rename ( JPATH_ROOT . '/' . $thumb, JPATH_ROOT . '/' . $data->thumb );

			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_MEDIA_MANAGER_MEDIA_RENAME_SUCCESS" ),
					'status' => true,
					'code' => 200
			];
		} catch ( Exception $e ) {
			$response = [ 
					'data' => $e->getMessage (),
					'status' => false,
					'code' => 500
			];
		}

		$code = $response ['code'];
		unset ( $response ['code'] );

		$app->setHeader ( 'status', $code, true );
		$app->sendHeaders ();
		echo new JsonResponse ( $response );
		$app->close ();
	}

	/**
	 * Upload media file function
	 *
	 * @return string
	 *
	 * @since 4.0.0
	 */
	public function upload_media() {
		$app = Factory::getApplication ( 'site' );
		$user = Factory::getApplication()->getIdentity();
		$canCreate = $user->authorise ( 'core.create', 'com_jpagebuilder' );

		if (! $canCreate) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_MEDIA_UPLOAD" ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		$model = $this->getModel ();
		$user = Factory::getApplication()->getIdentity();
		$input = Factory::getApplication ()->getInput();

		if (isset ( $_FILES ['file'] ) && $_FILES ['file']) {
			$file = $_FILES ['file'];

			$dir = $input->post->get ( 'folder', '', 'PATH' );
			$report = array ();

			$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder' ) || $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );

			if ($authorised !== true) {
				$report ['status'] = false;
				$report ['output'] = Text::_ ( 'JERROR_ALERTNOAUTHOR' );
				echo json_encode ( $report );
				die ();
			}

			if (count ( ( array ) $file )) {
				if ($file ['error'] == UPLOAD_ERR_OK) {
					$error = false;
					$params = ComponentHelper::getParams ( 'com_media' );
					$contentLength = ( int ) $_SERVER ['CONTENT_LENGTH'];
					$mediaHelper = new MediaHelper ();
					$postMaxSize = $mediaHelper->toBytes ( ini_get ( 'post_max_size' ) );
					$memoryLimit = $mediaHelper->toBytes ( ini_get ( 'memory_limit' ) );

					// Check for the total size of post back data.
					if (($postMaxSize > 0 && $contentLength > $postMaxSize) || ($memoryLimit != - 1 && $contentLength > $memoryLimit)) {
						$report ['status'] = false;
						$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_MEDIA_TOTAL_SIZE_EXCEEDS' );
						$error = true;
						echo json_encode ( $report );
						die ();
					}

					$uploadMaxSize = $params->get ( 'upload_maxsize', 0 ) * 1024 * 1024;
					$uploadMaxFileSize = $mediaHelper->toBytes ( ini_get ( 'upload_max_filesize' ) );

					if (($file ['error'] == 1) || ($uploadMaxSize > 0 && $file ['size'] > $uploadMaxSize) || ($uploadMaxFileSize > 0 && $file ['size'] > $uploadMaxFileSize)) {
						$report ['status'] = false;
						$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_MEDIA_LARGE' );
						$error = true;
					}

					// File formats
					$accepted_file_formats = array (
							'image' => array (
									'jpg',
									'jpeg',
									'png',
									'gif',
									'svg',
									'webp'
							),
							'video' => array (
									'mp4',
									'mov',
									'wmv',
									'avi',
									'mpg',
									'ogv',
									'3gp',
									'3g2'
							),
							'audio' => array (
									'mp3',
									'm4a',
									'ogg',
									'wav'
							),
							'attachment' => array (
									'pdf',
									'doc',
									'docx',
									'key',
									'ppt',
									'pptx',
									'pps',
									'ppsx',
									'odt',
									'xls',
									'xlsx',
									'zip',
									'json'
							)
					);

					// Upload if no error found
					if (! $error) {
						$date = Factory::getDate ();
						$file_ext = strtolower ( pathinfo($file ['name'], PATHINFO_EXTENSION) );

						if (self::in_array ( $file_ext, $accepted_file_formats )) {
							$media_type = self::array_search ( $file_ext, $accepted_file_formats );

							if ($media_type == 'image') {
								$mediaParams = ComponentHelper::getParams ( 'com_media' );
								$folder_root = $mediaParams->get ( 'file_path', 'files' ) . '/';
							} elseif ($media_type == 'video') {
								$folder_root = 'media/videos/';
							} elseif ($media_type == 'audio') {
								$folder_root = 'media/audios/';
							} elseif ($media_type == 'attachment') {
								$folder_root = 'media/attachments/';
							} elseif ($media_type == 'fonts') {
								$folder_root = 'media/fonts/';
							}

							$report ['type'] = $media_type;

							$folder = $folder_root . HTMLHelper::_ ( 'date', $date, 'Y' ) . '/' . HTMLHelper::_ ( 'date', $date, 'm' ) . '/' . HTMLHelper::_ ( 'date', $date, 'd' );

							if ($dir != '') {
								$folder = ltrim ( $dir, '/' );
							}

							if (! is_dir ( JPATH_ROOT . '/' . $folder )) {
								Folder::create ( JPATH_ROOT . '/' . $folder, 0755 );
							}

							if ($media_type == 'image') {
								if (! is_dir ( JPATH_ROOT . '/' . $folder . '/_jpmedia_thumbs' )) {
									Folder::create ( JPATH_ROOT . '/' . $folder . '/_jpmedia_thumbs', 0755 );
								}
							}

							$name = $file ['name'];
							$path = $file ['tmp_name'];
							// Do no override existing file

							$media_file = preg_replace ( '#\s+#', "-", File::makeSafe ( basename ( strtolower ( $name ) ) ) );
							$i = 0;
							do {
								$base_name = File::stripExt ( $media_file ) . ($i ? "$i" : "");
								$ext = pathinfo($media_file, PATHINFO_EXTENSION);
								$media_name = $base_name . '.' . $ext;
								$i ++;
								$dest = JPATH_ROOT . '/' . $folder . '/' . $media_name;
								$src = $folder . '/' . $media_name;
							} while ( file_exists ( $dest ) );
							// End Do not override

							if (File::upload ( $path, $dest, false, true )) {
								$media_attr = [ ];
								$thumb = '';

								if ($media_type == 'image') {
									list ( $imgWidth, $imgHeight ) = getimagesize ( $dest );

									if (strtolower ( $ext ) == 'svg') {
										$report ['src'] = Uri::root ( true ) . '/' . $src;
									} else {
										$image = new JpagebuilderHelperImage ( $dest );
										$media_attr ['full'] = [ 
												'height' => $image->height,
												'width' => $image->width
										];

										if (($image->width > 300) || ($image->height > 225)) {
											$thumbDestPath = dirname ( $dest ) . '/_jpmedia_thumbs';
											$created = $image->createThumb ( array (
													'300',
													'300'
											), $thumbDestPath, $base_name, $ext );

											if ($created == false) {
												$report ['status'] = false;
												$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_FILE_NOT_SUPPORTED' );
												$error = true;
												echo json_encode ( $report );
												die ();
											}

											$report ['src'] = Uri::root ( true ) . '/' . $folder . '/_jpmedia_thumbs/' . $base_name . '.' . $ext;
											$thumb = $folder . '/_jpmedia_thumbs/' . $base_name . '.' . $ext;
											$humbdest = $thumbDestPath . '/' . $base_name . '.' . $ext;
											list ( $width, $height ) = getimagesize ( $humbdest );
											$media_attr ['thumbnail'] = [ 
													'height' => $height,
													'width' => $width
											];
											$report ['thumb'] = $thumb;
										} else {
											$report ['src'] = Uri::root ( true ) . '/' . $src;
											$report ['thumb'] = $src;
										}
										// Create placeholder for lazy load
										$this->create_media_placeholder ( $dest, $base_name, $ext );
									}
								}

								$insertid = $model->insertMedia ( $base_name, $src, json_encode ( $media_attr ), $thumb, $media_type );
								$report ['media_attr'] = $media_attr;
								$report ['status'] = true;
								$report ['title'] = $base_name;
								$report ['id'] = $insertid;
								$report ['path'] = $src;

								$layout_path = JPATH_ROOT . '/administrator/components/com_jpagebuilder/layouts';
								$format_layout = new FileLayout ( 'media.format', $layout_path );
								$report ['output'] = $format_layout->render ( array (
										'media' => $model->getMediaByID ( $insertid ),
										'innerHTML' => true
								) );
							} else {
								$report ['status'] = false;
								$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_UPLOAD_FAILED' );
							}
						} else {
							$report ['status'] = false;
							$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_FILE_NOT_SUPPORTED' );
						}
					}
				}
			} else {
				$report ['status'] = false;
				$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_UPLOAD_FAILED' );
			}
		} else {
			$report ['status'] = false;
			$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_UPLOAD_FAILED' );
		}

		echo json_encode ( $report );
		die ();
	}
	public function delete_media() {
		$app = Factory::getApplication ( 'site' );
		$user = Factory::getApplication()->getIdentity();
		$canDelete = $user->authorise ( 'core.delete', 'com_jpagebuilder' );

		if (! $canDelete) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_MEDIA_DELETION" ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		$input = $app->getInput();
		$user = Factory::getApplication()->getIdentity();
		$model = $this->getModel ();

		$data = $input->json->get ( 'data', [ ], 'Array' );
		$response = [ 
				'status' => false,
				'data' => 'Something went wrong!'
		];

		if (empty ( $data )) {
			$app->setHeader ( 'status', 500, false );
			$app->sendHeaders ();
			echo new JsonResponse ( $response );
			$app->close ();
		}

		foreach ( $data as $item ) {
			if (! $this->removeMediaItem ( $item, $model, $user )) {
				continue;
			}

			$response ['data'] = 'Media item deleted!';
		}

		$response ['status'] = true;

		$app->setHeader ( 'status', 200, true );
		$app->sendHeaders ();
		echo new JsonResponse ( $response );
		$app->close ();
	}

	// Delete File
	public function deleteMediaItem() {
		$model = $this->getModel ();
		$user = Factory::getApplication()->getIdentity();
		$input = Factory::getApplication ()->getInput();
		$m_type = $input->post->get ( 'm_type', NULL, 'string' );

		if ($m_type == 'path') {
			$report = array ();
			$report ['status'] = true;
			$path = htmlspecialchars ( $input->post->get ( 'path', NULL, 'string' ) );
			$src = JPATH_ROOT . '/' . $path;

			if (file_exists ( $src )) {
				if (! File::delete ( $src )) {
					$report ['status'] = false;
					$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_DELETE_FAILED' );
					echo json_encode ( $report );
					die ();
				}
			} else {
				$report ['status'] = true;
			}

			echo json_encode ( $report );
		} else {
			$id = $input->post->get ( 'id', NULL, 'int' );

			if (! is_numeric ( $id )) {
				$report ['status'] = false;
				$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_DELETE_FAILED' );
				echo json_encode ( $report );
				die ();
			}

			$media = $model->getMediaByID ( $id );

			$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder' ) || ($user->authorise ( 'core.edit.own', 'com_jpagebuilder' ) && ($media->created_by == $user->id));

			if ($authorised !== true) {
				$report ['status'] = false;
				$report ['output'] = Text::_ ( 'JERROR_ALERTNOAUTHOR' );
				echo json_encode ( $report );
				die ();
			}

			$src = JPATH_ROOT . '/' . $media->path;

			$report = array ();
			$report ['status'] = false;

			if (isset ( $media->thumb ) && $media->thumb) {
				if (file_exists ( JPATH_ROOT . '/' . $media->thumb )) {
					File::delete ( JPATH_ROOT . '/' . $media->thumb ); // Delete thumb
				}
			}

			if (file_exists ( $src )) {
				// Delete placeholder too
				$this->delete_image_placeholder ( $src );

				if (! File::delete ( $src )) {
					$report ['status'] = false;
					$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_DELETE_FAILED' );
					echo json_encode ( $report );
					die ();
				}
			} else {
				$report ['status'] = true;
			}

			// Remove from database
			$media = $model->removeMediaByID ( $id );
			$report ['status'] = true;

			echo json_encode ( $report );
		}

		die ();
	}

	// Create folder
	public function create_folder() {
		$app = Factory::getApplication ( 'site' );
		$user = Factory::getApplication()->getIdentity();
		$canCreate = $user->authorise ( 'core.create', 'com_jpagebuilder' );

		if (! $canCreate) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_MEDIA_CREATION" ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		$input = Factory::getApplication ()->getInput();
		$folder = $input->post->get ( 'folder', '', 'string' );

		$dirname = dirname ( $folder );
		$basename = OutputFilter::stringURLSafe ( basename ( $folder ) );
		$folder = $dirname . '/' . $basename;

		$report = array ();
		$report ['status'] = false;
		$fullName = JPATH_ROOT . $folder;

		try {
			$fullName = BuilderMediaHelper::checkForMediaActionBoundary ( $fullName );
		} catch ( \Exception $e ) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'message' => $e->getMessage (),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		if (! JpagebuilderSecurityHelper::isActionableFolder ( $folder )) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'message' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_MEDIA_CREATION' ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		$folderToCreate = Path::clean ( JPATH_ROOT . $folder );

		if (! is_dir ( $folderToCreate )) {
			if (Folder::create ( $folderToCreate, 0755 )) {
				$report ['status'] = true;

				$folder_info ['name'] = basename ( $folder );
				$folder_info ['relname'] = $folder;
				$folder_info ['fullname'] = $fullName;
				$report ['output'] = $folder_info;
			} else {
				$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_FOLDER_CREATION_FAILED' );
			}
		} else {
			$report ['output'] = Text::_ ( 'COM_JPAGEBUILDER_MEDIA_MANAGER_FOLDER_EXISTS' );
		}

		echo json_encode ( $report );
		die ();
	}
	public function delete_folder() {
		$app = Factory::getApplication ( 'site' );
		$user = Factory::getApplication()->getIdentity();
		$canDelete = $user->authorise ( 'core.delete', 'com_jpagebuilder' );

		if (! $canDelete) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_MEDIA_DELETION" ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		$input = Factory::getApplication ()->getInput();
		$folder = $input->post->get ( 'folder', '', 'string' );
		$deleteItem = $input->post->get ( 'deleteItem', '', 'string' );
		$model = $this->getModel ();
		$dirname = dirname ( $folder );
		$basename = OutputFilter::stringURLSafe ( basename ( $folder ) );
		$folder = $dirname . '/' . $basename;
		$cleanedFullPath = Path::clean ( JPATH_ROOT . $folder );
		$report = array ();
		$report ['status'] = false;

		if (! JpagebuilderSecurityHelper::isActionableFolder ( $folder )) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'message' => Text::_ ( 'COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_FOLDER_DELETION' ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		if (! is_dir ( $cleanedFullPath )) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'message' => Text::_ ( "COM_JPAGEBUILDER_MEDIA_MANAGER_FOLDER_EXISTS" ),
					'status' => false,
					'code' => 500
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		if ($deleteItem === 'multiple') {
			$mediaDelete = $model->removeMediaByPath ( substr ( $folder, 1 ) . '/' );
		} else {
			$mediaDelete = true;
		}

		if ($mediaDelete === true) {
			if (Folder::delete ( $cleanedFullPath )) {
				$report ['status'] = true;
				$folder_info ['name'] = basename ( $folder );
				$folder_info ['relname'] = $folder;
				$report ['output'] = $folder_info;
			} else {
				$report ['output'] = Text::_ ( "COM_JPAGEBUILDER_MEDIA_FOLDER_DELETE_FAILED" );
			}
		} else {
			$report ['output'] = Text::_ ( "COM_JPAGEBUILDER_MEDIA_FILES_DELETE_FAILED" );
		}

		echo json_encode ( $report );
		die ();
	}
	public function rename_folder() {
		$app = Factory::getApplication ( 'site' );
		$user = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise ( 'core.edit', 'com_jpagebuilder' );

		if (! $canEdit) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_MEDIA_RENAME" ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		$input = Factory::getApplication ()->getInput();
		$model = $this->getModel ();
		$currentfolder = $input->post->get ( 'currentfolder', '', 'string' );
		$newfolder = $input->post->get ( 'newfolder', '', 'string' );
		$renameItem = $input->post->get ( 'renameItem', '', 'string' );
		$dirname = dirname ( $currentfolder );
		$currentbasename = OutputFilter::stringURLSafe ( basename ( $currentfolder ) );
		$newbasename = OutputFilter::stringURLSafe ( basename ( $newfolder ) );
		$src = $dirname . '/' . $currentbasename;
		$cleanedSrc = Path::clean ( JPATH_ROOT . $src );
		$dest = $dirname . '/' . $newbasename;
		$cleanedDest = Path::clean ( JPATH_ROOT . $dest );

		if (! JpagebuilderSecurityHelper::isActionableFolder ( $currentfolder ) || ! JpagebuilderSecurityHelper::isActionableFolder ( $newfolder )) {
			$app->setHeader ( 'status', 403, true );
			$app->sendHeaders ();
			$response = [ 
					'data' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_UNAUTHORIZED_FOLDER_RENAME" ),
					'status' => false,
					'code' => 403
			];
			echo new JsonResponse ( $response );
			$app->close ();
		}

		if (is_dir ( Path::clean ( JPATH_ROOT . $currentfolder ) )) {
			if ($renameItem === 'multiple') {
				$mediaRename = $model->editMediaPathById ( substr ( $src, 1 ) . '/', substr ( $dest, 1 ) . '/' );
			} else {
				$mediaRename = true;
			}

			if ($mediaRename === true) {
				if (Folder::move ( $cleanedSrc, $cleanedDest, $path = '', $use_streams = false )) {
					$report ['status'] = true;
					$folder_info ['name'] = basename ( $dest );
					$folder_info ['relname'] = $dest;
					$folder_info ['fullname'] = JPATH_ROOT . $dest;
					$report ['output'] = $folder_info;
				} else {
					$report ['output'] = Text::_ ( "COM_JPAGEBUILDER_MEDIA_FOLDER_RENAME_FAILED" );
				}
			} else {
				$report ['output'] = $mediaRename;
				// 'MEDIA FILES COULD NOT BE RENAMED';
			}
		} else {
			$report ['output'] = Text::_ ( "COM_JPAGEBUILDER_MEDIA_FOLDER_NOT_FOUND" );
		}
		echo json_encode ( $report );
		die ();
	}
	public function __construct($config = [ ]) {
		parent::__construct ( $config );

		// check have access
		$user = Factory::getApplication()->getIdentity();
		$authorised = $user->authorise ( 'core.admin', 'com_jpagebuilder' ) || $user->authorise ( 'core.manage', 'com_jpagebuilder' );

		if (! $authorised) {
			$response = [ 
					'status' => false,
					'message' => Text::_ ( 'JERROR_ALERTNOAUTHOR' )
			];

			echo json_encode ( $response );
			die ();
		}
	}
}
