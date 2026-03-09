<?php
namespace Pablop76\Plugin\Content\Articlelimit\Extension;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

class Articlelimit extends CMSPlugin implements SubscriberInterface
{
    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return [
            'onContentBeforeSave' => 'checkArticleLimit',
            'onContentPrepareForm' => 'displayLimitInfo'
        ];
    }

    /**
     * Sprawdza limit artykułów przed zapisem
     */
    public function checkArticleLimit($event)
    {
        [$context, $article, $isNew] = array_values($event->getArguments());
        
        // Sprawdzamy czy dotyczy to artykułów
        $allowedContexts = ['com_content.article', 'com_content.form', 'com_content'];
        if (!in_array($context, $allowedContexts)) {
            return true;
        }

        // Sprawdzamy czy to nowy artykuł
        $isReallyNew = $isNew || (is_object($article) && empty($article->id));
        if (!$isReallyNew) {
            return true;
        }

        $user = Factory::getUser();
        
        // Administratorzy mają nieograniczony dostęp
        if ($user->authorise('core.admin')) {
            return true;
        }

        // Pobierz limit użytkownika
        $userLimit = $this->getUserLimit($user->id);

        // Jeśli limit jest 0 - brak ograniczeń
        if ($userLimit === 0) {
            return true;
        }

        // Pobierz liczbę istniejących artykułów użytkownika
        $currentCount = $this->getUserArticleCount($user->id);

        // Sprawdź czy użytkownik nie przekroczył limitu
        if ($currentCount >= $userLimit) {
            $message = Text::sprintf('PLG_USER_ARTICLELIMIT_LIMIT_EXCEEDED', $userLimit);
            throw new \RuntimeException($message);
        }

        return true;
    }

    /**
     * Wyświetla informację o limicie artykułów w formularzu
     */
    public function displayLimitInfo($event)
    {
        $app = Factory::getApplication();
    

        [$form, $data] = array_values($event->getArguments());
        
        $allowedForms = ['com_content.article', 'com_content.form'];
        if (in_array($form->getName(), $allowedForms)) {
            $user = Factory::getUser();
            $currentCount = $this->getUserArticleCount($user->id);
            $userLimit = $this->getUserLimit($user->id);
            
            if ($userLimit > 0) {
                $message = Text::sprintf('PLG_USER_ARTICLELIMIT_CURRENT_COUNT', $currentCount, $userLimit);
                $app->enqueueMessage($message, 'info');
            }
        }

        return true;
    }

    /**
     * Określa limit artykułów na podstawie grup użytkownika
     */
    private function getUserLimit($userId)
    {
        $user = Factory::getUser($userId);
        
        // Sprawdź czy użytkownik w ogóle może tworzyć artykuły
        if (!$user->authorise('core.create', 'com_content')) {
            return 0; // Nie może tworzyć = brak limitu (i tak nie napisze)
        }

        $userGroups = $user->getAuthorisedGroups();

        // Pobierz limity z parametrów wtyczki
        $defaultLimit = (int)$this->params->get('default_limit', 0);
        $authorLimit = (int)$this->params->get('author_limit', 0);
        $editorLimit = (int)$this->params->get('editor_limit', 0);
        $publisherLimit = (int)$this->params->get('publisher_limit', 0);

        $groups = [
            3 => $authorLimit,     // Author
            4 => $editorLimit,     // Editor  
            5 => $publisherLimit,  // Publisher
        ];

        foreach ($groups as $groupId => $limit) {
            if (in_array($groupId, $userGroups)) {
                return $limit;
            }
        }

        return $defaultLimit;
    }

    /**
     * Pobiera liczbę artykułów użytkownika z uwzględnieniem kategorii i stanów
     */
    private function getUserArticleCount($userId)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->select('COUNT(*)')
            ->from($db->quoteName('#__content'))
            ->where($db->quoteName('created_by') . ' = ' . (int)$userId);

        // Filtruj stany artykułów
        $countStates = $this->params->get('count_states', ['1', '0', '2']);
        
        if (!empty($countStates)) {
            $query->where($db->quoteName('state') . ' IN (' . implode(',', array_map('intval', (array)$countStates)) . ')');
        } else {
            // Domyślnie uwzględnij wszystkie stany oprócz usuniętych
            $query->where($db->quoteName('state') . ' >= 0');
        }

        // Filtruj kategorie - wyklucz
        $excludeCategories = $this->params->get('exclude_categories', []);
        
        if (!empty($excludeCategories) && is_array($excludeCategories)) {
            // Filtruj puste wartości (jak "")
            $filteredCategories = array_filter($excludeCategories, function($cat) {
                return !empty($cat) && $cat !== "" && $cat !== "0";
            });
            
            if (!empty($filteredCategories)) {
                $query->where($db->quoteName('catid') . ' NOT IN (' . implode(',', array_map('intval', $filteredCategories)) . ')');
            }
        }

        $db->setQuery($query);
        return (int)$db->loadResult();
    }
}