/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Action.jsx":
/*!******************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Action.jsx ***!
  \******************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Action)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _FieldsDropdown__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FieldsDropdown */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldsDropdown.jsx");
/* harmony import */ var _Trigger__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Trigger */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Trigger.jsx");
/* harmony import */ var _FieldValue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./FieldValue */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldValue.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }




var Action = /*#__PURE__*/function (_React$Component) {
  function Action() {
    _classCallCheck(this, Action);
    return _callSuper(this, Action, arguments);
  }
  _inherits(Action, _React$Component);
  return _createClass(Action, [{
    key: "render",
    value: function render() {
      var _this = this;
      var selected_field = this.props.form_fields.find(function (i) {
        return i.id == _this.props.action.field;
      });
      return /*#__PURE__*/React.createElement("div", {
        className: "action options"
      }, /*#__PURE__*/React.createElement(_FieldsDropdown__WEBPACK_IMPORTED_MODULE_1__["default"], {
        value: this.props.action.field,
        form_fields: this.props.form_fields,
        onChange: this.props.onChange
      }), this.props.action.field && /*#__PURE__*/React.createElement(_Trigger__WEBPACK_IMPORTED_MODULE_2__["default"], {
        value: this.props.action.trigger,
        selected_field: selected_field,
        onChange: this.props.onChange
      }), /*#__PURE__*/React.createElement(_FieldValue__WEBPACK_IMPORTED_MODULE_3__["default"], {
        action: this.props.action,
        value: this.props.action.arg,
        selected_field: selected_field,
        onChange: this.props.onChange
      }), /*#__PURE__*/React.createElement("span", {
        className: "clb-btns"
      }, /*#__PURE__*/React.createElement("button", {
        className: "cf-icon-cancel",
        title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('DELETE_ACTION'),
        onClick: this.props.onDelete
      })));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Actions.jsx":
/*!*******************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Actions.jsx ***!
  \*******************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Actions)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _Action__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Action */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Action.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }


var Actions = /*#__PURE__*/function (_React$Component) {
  function Actions() {
    _classCallCheck(this, Actions);
    return _callSuper(this, Actions, arguments);
  }
  _inherits(Actions, _React$Component);
  return _createClass(Actions, [{
    key: "render",
    value: function render() {
      var _this = this;
      var count = this.props.actions ? Object.entries(this.props.actions).length : 0;
      return /*#__PURE__*/React.createElement("div", {
        className: "actions"
      }, count > 0 && Object.entries(this.props.actions).map(function (_ref) {
        var _ref2 = _slicedToArray(_ref, 2),
          id = _ref2[0],
          action = _ref2[1];
        return /*#__PURE__*/React.createElement(_Action__WEBPACK_IMPORTED_MODULE_1__["default"], {
          key: id,
          action: action,
          form_fields: _this.props.form_fields,
          onAdd: function onAdd() {
            return _this.props.model.add(_this.props.condition_id);
          },
          onChange: function onChange(e) {
            return _this.props.model.update(e, id, _this.props.condition_id);
          },
          onDelete: function onDelete() {
            return _this.props.model["delete"](id, _this.props.condition_id);
          }
        });
      }), /*#__PURE__*/React.createElement("button", {
        className: "cf-btn cf-icon-plus",
        title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ADD_ACTION'),
        onClick: function onClick() {
          return _this.props.model.add(_this.props.condition_id);
        }
      }));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/App.jsx":
/*!***************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/App.jsx ***!
  \***************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ App)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

var App = /*#__PURE__*/function (_React$Component) {
  function App(props) {
    var _this;
    _classCallCheck(this, App);
    _this = _callSuper(this, App, [props]);
    _defineProperty(_this, "modelRules", function (condition_id) {
      return {
        addGroup: function addGroup() {
          var conditions = _this.state.conditions;
          var conditionRules = conditions[condition_id]['rules'];
          var newItem = _defineProperty({}, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID(), _defineProperty({}, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID(), {}));
          conditionRules = Object.assign(conditionRules, newItem);
          _this.setState({
            conditions: conditions
          });
        },
        add: function add(rule_group_id) {
          var conditions = _this.state.conditions;
          var conditionRules = conditions[condition_id]['rules'];
          var newItem = _defineProperty({}, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID(), {});
          conditionRules[rule_group_id] = Object.assign(conditionRules[rule_group_id], newItem);
          _this.setState({
            conditions: conditions
          });
        },
        "delete": function _delete(rule_group_id, rule_id) {
          var conditions = _this.state.conditions;
          var conditionRules = conditions[condition_id]['rules'];
          delete conditionRules[rule_group_id][rule_id];

          // Delete the whole group if no rules left
          var groupIsEmpty = !Object.keys(conditionRules[rule_group_id]).length;
          if (groupIsEmpty) {
            delete conditionRules[rule_group_id];
          }
          _this.setState({
            conditions: conditions
          });
        },
        update: function update(event, rule_group_id, rule_id) {
          var conditions = _this.state.conditions;
          var rule = conditions[condition_id]['rules'][rule_group_id][rule_id];
          Object.assign(rule, _defineProperty({}, event.target.name, event.target.value));
          _this.setState({
            conditions: conditions
          });
        }
      };
    });
    _defineProperty(_this, "model", function (namespace) {
      return {
        add: function add(condition_id) {
          var conditions = _this.state.conditions;
          var newItem = _defineProperty({}, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID(), {});
          conditions[condition_id][namespace] = Object.assign(conditions[condition_id][namespace] || {}, newItem);
          _this.setState({
            conditions: conditions
          });
        },
        update: function update(event, property_id, condition_id) {
          var conditions = _this.state.conditions;

          // Action data
          var data_current = conditions[condition_id][namespace][property_id];
          var data_new = Object.assign(data_current, _defineProperty({}, event.target.name, event.target.value));
          conditions[condition_id][namespace][property_id] = data_new;
          _this.setState({
            conditions: conditions
          });
        },
        "delete": function _delete(id, condition_id) {
          var conditions = _this.state.conditions;
          delete conditions[condition_id][namespace][id];
          _this.setState({
            conditions: conditions
          });
        }
      };
    });
    // Condition Model
    _defineProperty(_this, "modelCondition", {
      add: function add() {
        var newConditionKey = _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID();
        var newcondition = _defineProperty({}, newConditionKey, {
          label: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NEW_CONDITION'),
          rules: _defineProperty({}, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID(), _defineProperty({}, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID(), {})),
          actions: _defineProperty({}, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID(), {})
        });
        var conditions = Object.assign(_this.state.conditions, newcondition);
        _this.setState({
          conditions: conditions
        });
        _this.modelCondition.show(newConditionKey);
      },
      "delete": function _delete(id) {
        var conditions = _this.state.conditions;
        delete conditions[id];
        _this.setState({
          conditions: conditions
        });
      },
      update: function update(condition_id, property_name, value) {
        var conditions = _this.state.conditions;
        conditions[condition_id][property_name] = value;
        _this.setState({
          conditions: conditions
        });
      },
      getActive: function getActive() {
        var ids = Object.keys(_this.state.conditions);
        if (!ids) {
          return;
        }
        if (ids.includes(_this.state.active_condition)) {
          return _this.state.active_condition;
        }
        return ids[0];
      },
      show: function show(id) {
        _this.setState({
          active_condition: id
        });
      },
      copy: function copy(id) {
        var newConditionKey = _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].getRandomID();

        // Get a deep copy of the condition. Quick and dirty way to deep cloning an object.
        var cloneCondition = JSON.parse(JSON.stringify(_this.state.conditions[id]));
        cloneCondition.label = cloneCondition.label + ' (2)';
        var newcondition = _defineProperty({}, newConditionKey, cloneCondition);
        var conditions = Object.assign(_this.state.conditions, newcondition);
        _this.setState({
          conditions: conditions
        });
        _this.modelCondition.show(newConditionKey);
      }
    });
    _this.state = {
      conditions: {},
      el: null,
      form_fields: null,
      active_condition: null
    };
    _this.builder = $builder.get(0);
    return _this;
  }
  _inherits(App, _React$Component);
  return _createClass(App, [{
    key: "componentDidUpdate",
    value: function componentDidUpdate() {
      // Update the hidden field with the state
      this.state.el.value = JSON.stringify(this.state.conditions);
    }
  }, {
    key: "getFields",
    value: function getFields() {
      return ConvertFormsBuilder.FieldsHelper.getFieldsArray();
    }
  }, {
    key: "componentDidMount",
    value: function componentDidMount() {
      var _this2 = this;
      this.setState({
        el: this.props.el,
        conditions: this.props.el.value ? JSON.parse(this.props.el.value) : {},
        form_fields: this.getFields()
      });
      this.builder.addEventListener('fields.update', function () {
        _this2.setState({
          form_fields: _this2.getFields()
        });
      });
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/AppFields.jsx":
/*!*********************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/AppFields.jsx ***!
  \*********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ AppFields)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _App__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./App */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/App.jsx");
/* harmony import */ var _ConditionNav__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./ConditionNav */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/ConditionNav.jsx");
/* harmony import */ var _Condition__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./Condition */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Condition.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _slicedToArray(r, e) { return _arrayWithHoles(r) || _iterableToArrayLimit(r, e) || _unsupportedIterableToArray(r, e) || _nonIterableRest(); }
function _nonIterableRest() { throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method."); }
function _unsupportedIterableToArray(r, a) { if (r) { if ("string" == typeof r) return _arrayLikeToArray(r, a); var t = {}.toString.call(r).slice(8, -1); return "Object" === t && r.constructor && (t = r.constructor.name), "Map" === t || "Set" === t ? Array.from(r) : "Arguments" === t || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(t) ? _arrayLikeToArray(r, a) : void 0; } }
function _arrayLikeToArray(r, a) { (null == a || a > r.length) && (a = r.length); for (var e = 0, n = Array(a); e < a; e++) n[e] = r[e]; return n; }
function _iterableToArrayLimit(r, l) { var t = null == r ? null : "undefined" != typeof Symbol && r[Symbol.iterator] || r["@@iterator"]; if (null != t) { var e, n, i, u, a = [], f = !0, o = !1; try { if (i = (t = t.call(r)).next, 0 === l) { if (Object(t) !== t) return; f = !1; } else for (; !(f = (e = i.call(t)).done) && (a.push(e.value), a.length !== l); f = !0); } catch (r) { o = !0, n = r; } finally { try { if (!f && null != t["return"] && (u = t["return"](), Object(u) !== u)) return; } finally { if (o) throw n; } } return a; } }
function _arrayWithHoles(r) { if (Array.isArray(r)) return r; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }




var AppFields = /*#__PURE__*/function (_App) {
  function AppFields() {
    _classCallCheck(this, AppFields);
    return _callSuper(this, AppFields, arguments);
  }
  _inherits(AppFields, _App);
  return _createClass(AppFields, [{
    key: "render",
    value: function render() {
      var _this = this;
      var active_condition = this.modelCondition.getActive();
      var containerClass = 'clb ' + (!active_condition ? 'clb-empty' : '');
      return /*#__PURE__*/React.createElement("div", {
        className: containerClass
      }, /*#__PURE__*/React.createElement("div", {
        className: "clb-left"
      }, /*#__PURE__*/React.createElement("div", {
        className: "conditionNavList"
      }, Object.entries(this.state.conditions).map(function (_ref) {
        var _ref2 = _slicedToArray(_ref, 2),
          id = _ref2[0],
          condition = _ref2[1];
        return /*#__PURE__*/React.createElement(_ConditionNav__WEBPACK_IMPORTED_MODULE_2__["default"], {
          key: id,
          id: id,
          active: active_condition,
          condition: condition,
          modelCondition: _this.modelCondition
        });
      })), !active_condition && /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", null, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NO_CONDITIONS')), /*#__PURE__*/React.createElement("div", {
        className: "cf-icon-unhappy"
      })), /*#__PURE__*/React.createElement("button", {
        className: "cf-btn cf-icon-plus",
        onClick: this.modelCondition.add
      }, " ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ADD_CONDITION'))), /*#__PURE__*/React.createElement("div", {
        className: "clb-right"
      }, /*#__PURE__*/React.createElement("div", {
        className: "conditions"
      }, Object.entries(this.state.conditions).map(function (_ref3) {
        var _ref4 = _slicedToArray(_ref3, 2),
          id = _ref4[0],
          condition = _ref4[1];
        return /*#__PURE__*/React.createElement(_Condition__WEBPACK_IMPORTED_MODULE_3__["default"], {
          key: id,
          id: id,
          active: active_condition,
          modelCondition: _this.modelCondition,
          modelRule: _this.modelRules,
          modelAction: _this.model('actions'),
          modelActionElse: _this.model('else'),
          condition: condition,
          form_fields: _this.state.form_fields
        });
      }))));
    }
  }]);
}(_App__WEBPACK_IMPORTED_MODULE_1__["default"]);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/AppRules.jsx":
/*!********************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/AppRules.jsx ***!
  \********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ AppRules)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _App__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./App */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/App.jsx");
/* harmony import */ var _Rules__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Rules */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Rules.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }



var AppRules = /*#__PURE__*/function (_App) {
  function AppRules() {
    _classCallCheck(this, AppRules);
    return _callSuper(this, AppRules, arguments);
  }
  _inherits(AppRules, _App);
  return _createClass(AppRules, [{
    key: "render",
    value: function render() {
      var id = Object.keys(this.state.conditions)[0];
      var condition = this.state.conditions[id];
      return /*#__PURE__*/React.createElement("div", {
        className: "conditions"
      }, !id && /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement("div", null, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NO_CONDITIONS')), /*#__PURE__*/React.createElement("div", {
        className: "cf-icon-unhappy"
      }), /*#__PURE__*/React.createElement("button", {
        className: "cf-btn cf-icon-plus",
        onClick: this.modelCondition.add
      }, " ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ADD_CONDITION'))), id && /*#__PURE__*/React.createElement("div", {
        "class": "conditionSection"
      }, /*#__PURE__*/React.createElement(_Rules__WEBPACK_IMPORTED_MODULE_2__["default"], {
        rules: condition.rules,
        condition: condition,
        condition_id: id,
        model: this.modelRules,
        form_fields: this.state.form_fields
      })));
    }
  }]);
}(_App__WEBPACK_IMPORTED_MODULE_1__["default"]);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Comparator.jsx":
/*!**********************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Comparator.jsx ***!
  \**********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Comparator)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

var Comparator = /*#__PURE__*/function (_React$Component) {
  function Comparator() {
    var _this;
    _classCallCheck(this, Comparator);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Comparator, [].concat(args));
    _defineProperty(_this, "getOptions", function () {
      if (!_this.props.selected_field) {
        return {};
      }
      var options = {
        empty: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('EMPTY'),
        equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('EQUALS'),
        updates: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('UPDATES'),
        selected: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('HAS_SELECTED'),
        contains: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('CONTAINS'),
        starts_with: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('STARTS_WITH'),
        ends_with: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ENDS_WITH'),
        regex: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('REGEX'),
        not_empty: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_EMPTY'),
        not_equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_EQUALS'),
        not_selected: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_SELECTED'),
        not_contains: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_CONTAIN'),
        not_start_swith: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_START_WITH'),
        not_ends_with: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_END_WITH'),
        not_regex: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_REGEX'),
        less_than: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('LESS_THAN'),
        less_equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('LESS_THAN_EQUAL'),
        greater_than: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('GREATER_THAN'),
        greater_equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('GREATER_THAN_EQUAL')
      };
      var optionsForCheckboxes = {
        total_checked_equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('TOTAL_CHECKED_EQUAL'),
        total_checked_not_equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('TOTAL_CHECKED_NOT_EQUALS'),
        total_checked_less_than: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('TOTAL_CHECKED_LESS'),
        total_checked_less_equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('TOTAL_CHECKED_LESS_THAN_OR'),
        total_checked_greater_than: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('TOTAL_CHECKED_GREATER'),
        total_checked_greater_equals: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('TOTAL_CHECKED_GREATER_THAN_OR')
      };
      switch (_this.props.selected_field.type) {
        case 'email':
          delete options.less_than;
          delete options.less_equals;
          delete options.greater_than;
          delete options.greater_equals;
          break;
        case 'checkbox':
          options = _objectSpread(_objectSpread({}, options), optionsForCheckboxes);
          break;
        case 'termsofservice':
          return {
            is_checked: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('IS_CHECKED'),
            not_checked: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NOT_CHECKED')
          };
          // removed by dead control flow

        case 'dropdown':
        case 'radio':
          delete options.selected;
          delete options.not_selected;
          break;
      }
      if (_this.props.selected_field.options) {
        delete options.contains;
        delete options.not_contains;
      } else {
        delete options.selected;
        delete options.not_selected;
      }
      return options;
    });
    return _this;
  }
  _inherits(Comparator, _React$Component);
  return _createClass(Comparator, [{
    key: "render",
    value: function render() {
      var options = this.getOptions();
      return /*#__PURE__*/React.createElement("select", {
        name: "comparator",
        onChange: this.props.onChange,
        value: this.props.value,
        className: "operator"
      }, /*#__PURE__*/React.createElement("option", {
        value: ""
      }, "- ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SELECT_OPERATOR'), " -"), Object.keys(options).map(function (key) {
        return /*#__PURE__*/React.createElement("option", {
          key: key,
          value: key
        }, options[key]);
      }));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Condition.jsx":
/*!*********************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Condition.jsx ***!
  \*********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Condition)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _Actions__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Actions */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Actions.jsx");
/* harmony import */ var _Rules__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Rules */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Rules.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }



var Condition = /*#__PURE__*/function (_React$Component) {
  function Condition() {
    var _this;
    _classCallCheck(this, Condition);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Condition, [].concat(args));
    // Find all fields used in rules
    _defineProperty(_this, "getFieldsUsedInRules", function () {
      var fields_in_rules = [];
      Object.values(_this.props.condition.rules).forEach(function (ruleGroup) {
        Object.values(ruleGroup).forEach(function (rule) {
          var id = parseInt(rule.field);
          if (rule.field && !fields_in_rules.includes(id)) {
            fields_in_rules.push(id);
          }
        });
      });
      return fields_in_rules;
    });
    _defineProperty(_this, "getComparatorsUsedInRules", function () {
      var usedComparators = [];
      Object.values(_this.props.condition.rules).forEach(function (ruleGroup) {
        Object.values(ruleGroup).forEach(function (rule) {
          if (rule.comparator && !usedComparators.includes(rule.comparator)) {
            usedComparators.push(rule.comparator);
          }
        });
      });
      return usedComparators;
    });
    /**	
     * In Actions we do not allow selecting a field that is being used in the Rules
     */
    _defineProperty(_this, "getValidFieldsForActions", function () {
      var fir = _this.getFieldsUsedInRules();
      return _this.props.form_fields.filter(function (i) {
        return !fir.includes(i.id);
      });
    });
    return _this;
  }
  _inherits(Condition, _React$Component);
  return _createClass(Condition, [{
    key: "render",
    value: function render() {
      var condition = this.props.condition;
      var cssClass = 'condition ' + (this.props.active == this.props.id ? 'active' : '');
      var fieldsForActions = this.getValidFieldsForActions();

      // When the Condition make use only of the 'Updates' comparator which triggers whenever the field's value is uopdated, it doesn't make sense to ask the user for alternative actions.
      var conditionUniqueComparators = this.getComparatorsUsedInRules();
      var conditionUsesOnlyTheUpdateComparator = conditionUniqueComparators.length == 1 && conditionUniqueComparators[0] == 'updates';
      return /*#__PURE__*/React.createElement("div", {
        className: cssClass
      }, /*#__PURE__*/React.createElement("div", {
        className: "conditionSection rules"
      }, /*#__PURE__*/React.createElement("h3", null, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('RULES_ALIAS')), /*#__PURE__*/React.createElement(_Rules__WEBPACK_IMPORTED_MODULE_2__["default"], {
        rules: condition.rules,
        condition: condition,
        condition_id: this.props.id,
        model: this.props.modelRule,
        form_fields: this.props.form_fields
      })), /*#__PURE__*/React.createElement("div", {
        className: "conditionSection actions"
      }, /*#__PURE__*/React.createElement("h3", null, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ACTIONS_ALIAS')), /*#__PURE__*/React.createElement(_Actions__WEBPACK_IMPORTED_MODULE_1__["default"], {
        actions: condition.actions,
        condition_id: this.props.id,
        model: this.props.modelAction,
        form_fields: fieldsForActions
      })), !conditionUsesOnlyTheUpdateComparator && /*#__PURE__*/React.createElement("div", {
        className: "conditionSection actionselse"
      }, /*#__PURE__*/React.createElement("h3", null, _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('CONDITION_ELSE')), /*#__PURE__*/React.createElement(_Actions__WEBPACK_IMPORTED_MODULE_1__["default"], {
        actions: condition["else"],
        condition_id: this.props.id,
        model: this.props.modelActionElse,
        form_fields: fieldsForActions
      })));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/ConditionNav.jsx":
/*!************************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/ConditionNav.jsx ***!
  \************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ConditionNav)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

var ConditionNav = /*#__PURE__*/function (_React$Component) {
  function ConditionNav() {
    var _this;
    _classCallCheck(this, ConditionNav);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, ConditionNav, [].concat(args));
    _defineProperty(_this, "blur", function () {
      if (!_this.props.condition.label) {
        _this.setLabel(_Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('NEW_CONDITION'));
      }
    });
    return _this;
  }
  _inherits(ConditionNav, _React$Component);
  return _createClass(ConditionNav, [{
    key: "render",
    value: function render() {
      var _this2 = this;
      var cssClass = 'conditionNav ' + (this.props.active == this.props.id ? 'active' : '');
      return /*#__PURE__*/React.createElement("div", {
        className: cssClass
      }, /*#__PURE__*/React.createElement("input", {
        type: "text",
        name: "label",
        className: "Mui",
        placeholder: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ENTER_TITLE'),
        value: this.props.condition.label,
        onBlur: this.blur,
        onFocus: function onFocus() {
          return _this2.props.modelCondition.show(_this2.props.id);
        },
        onChange: function onChange(e) {
          return _this2.props.modelCondition.update(_this2.props.id, 'label', e.target.value);
        }
      }), /*#__PURE__*/React.createElement("div", {
        className: "toolbar"
      }, /*#__PURE__*/React.createElement("button", {
        className: "cf-icon-cancel",
        onClick: function onClick() {
          return _this2.props.modelCondition["delete"](_this2.props.id);
        },
        title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('DELETE_CONDITION')
      }), /*#__PURE__*/React.createElement("button", {
        className: "cf-icon-copy",
        onClick: function onClick() {
          return _this2.props.modelCondition.copy(_this2.props.id);
        },
        title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('COPY_CONDITION')
      })));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldValue.jsx":
/*!**********************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldValue.jsx ***!
  \**********************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FieldValue)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _FieldsDropdown__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FieldsDropdown */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldsDropdown.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }


var FieldValue = /*#__PURE__*/function (_React$Component) {
  function FieldValue() {
    _classCallCheck(this, FieldValue);
    return _callSuper(this, FieldValue, arguments);
  }
  _inherits(FieldValue, _React$Component);
  return _createClass(FieldValue, [{
    key: "render",
    value: function render() {
      if (!this.props.selected_field || !this.props.action || !this.props.action.trigger) {
        return null;
      }
      if (this.props.action.trigger == 'copy_value') {
        // We do not rely on the formFields send by props as this property does not include the field used in the "When" section per are rules for all triggers.
        // The copy_value trigger though is the exception.
        var formFields = ConvertFormsBuilder.FieldsHelper.getFieldsArray();
        return /*#__PURE__*/React.createElement(_FieldsDropdown__WEBPACK_IMPORTED_MODULE_1__["default"], {
          name: "arg",
          inputsOnly: true,
          value: this.props.value || '',
          exclude: this.props.action.field,
          form_fields: formFields,
          onChange: this.props.onChange
        });
      }
      if (this.props.action.trigger.endsWith('option') || this.props.action.trigger.includes(['change_value'])) {
        if (this.props.selected_field.options && !this.props.action.trigger.includes('regex')) {
          return /*#__PURE__*/React.createElement("select", {
            className: "fieldValue",
            name: "arg",
            onChange: this.props.onChange,
            value: this.props.value || ''
          }, /*#__PURE__*/React.createElement("option", {
            value: ""
          }, "- ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SELECT_OPTION'), " -"), this.props.selected_field.options.map(function (option) {
            return /*#__PURE__*/React.createElement("option", {
              key: option.value,
              value: option.value
            }, option.label);
          }));
        } else {
          return /*#__PURE__*/React.createElement("input", {
            type: "text",
            name: "arg",
            placeholder: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ENTER_VALUE'),
            onChange: this.props.onChange,
            value: this.props.value || '',
            className: "fieldValue"
          });
        }
      }
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldsDropdown.jsx":
/*!**************************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldsDropdown.jsx ***!
  \**************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FieldsDropdown)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }

var FieldsDropdown = /*#__PURE__*/function (_React$Component) {
  function FieldsDropdown() {
    _classCallCheck(this, FieldsDropdown);
    return _callSuper(this, FieldsDropdown, arguments);
  }
  _inherits(FieldsDropdown, _React$Component);
  return _createClass(FieldsDropdown, [{
    key: "getFields",
    value: function getFields() {
      var _this = this;
      var formFields = this.props.form_fields;
      if (this.props.exclude) {
        formFields = formFields.filter(function (field) {
          return field.id != _this.props.exclude;
        });
      }
      if (this.props.inputsOnly) {
        return formFields.filter(function (field) {
          return field.name ? field : false;
        });
      }
      return formFields;
    }
  }, {
    key: "render",
    value: function render() {
      var fields = this.getFields();
      return /*#__PURE__*/React.createElement("select", {
        name: this.props.name ? this.props.name : 'field',
        value: this.props.value,
        onChange: this.props.onChange,
        className: "fieldName"
      }, /*#__PURE__*/React.createElement("option", {
        value: ""
      }, "- ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SELECT_FIELD'), " -"), fields && fields.map(function (option) {
        return /*#__PURE__*/React.createElement("option", {
          key: option.id,
          "data-type": option.type,
          value: option.id
        }, option.label);
      }));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Rule.jsx":
/*!****************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Rule.jsx ***!
  \****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Rule)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _FieldsDropdown__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./FieldsDropdown */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldsDropdown.jsx");
/* harmony import */ var _Comparator__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Comparator */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Comparator.jsx");
/* harmony import */ var _FieldValue__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./FieldValue */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/FieldValue.jsx");
/* harmony import */ var _RuleComparatorValue__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(/*! ./RuleComparatorValue */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/RuleComparatorValue.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }





var Rule = /*#__PURE__*/function (_React$Component) {
  function Rule() {
    _classCallCheck(this, Rule);
    return _callSuper(this, Rule, arguments);
  }
  _inherits(Rule, _React$Component);
  return _createClass(Rule, [{
    key: "render",
    value: function render() {
      var _this = this;
      var selected_field = this.props.form_fields.find(function (i) {
        return i.id == _this.props.rule.field;
      });
      return /*#__PURE__*/React.createElement("div", {
        className: "rule options"
      }, /*#__PURE__*/React.createElement(_FieldsDropdown__WEBPACK_IMPORTED_MODULE_1__["default"], {
        value: this.props.rule.field,
        form_fields: this.props.form_fields,
        onChange: this.props.onChange,
        inputsOnly: "true"
      }), this.props.rule.field && /*#__PURE__*/React.createElement(_Comparator__WEBPACK_IMPORTED_MODULE_2__["default"], {
        value: this.props.rule.comparator,
        selected_field: selected_field,
        onChange: this.props.onChange
      }), this.props.rule.comparator && !['is_checked', 'not_checked', 'empty', 'not_empty', 'updates'].includes(this.props.rule.comparator) && /*#__PURE__*/React.createElement(_RuleComparatorValue__WEBPACK_IMPORTED_MODULE_4__["default"], {
        value: this.props.rule.arg,
        selected_field: this.props.rule.comparator.includes('selected') || ['equals', 'not_equals'].includes(this.props.rule.comparator) ? selected_field : '',
        onChange: this.props.onChange
      }), /*#__PURE__*/React.createElement("span", {
        className: "clb-btns"
      }, /*#__PURE__*/React.createElement("button", {
        className: "cf-icon-cancel",
        title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('DELETE_RULE'),
        onClick: this.props.onDelete
      })));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/RuleComparatorValue.jsx":
/*!*******************************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/RuleComparatorValue.jsx ***!
  \*******************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ RuleComparatorValue)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }

var RuleComparatorValue = /*#__PURE__*/function (_React$Component) {
  function RuleComparatorValue() {
    _classCallCheck(this, RuleComparatorValue);
    return _callSuper(this, RuleComparatorValue, arguments);
  }
  _inherits(RuleComparatorValue, _React$Component);
  return _createClass(RuleComparatorValue, [{
    key: "render",
    value: function render() {
      if (this.props.selected_field && this.props.selected_field.options) {
        return /*#__PURE__*/React.createElement("select", {
          className: "fieldValue",
          name: "arg",
          onChange: this.props.onChange,
          value: this.props.value || ''
        }, /*#__PURE__*/React.createElement("option", {
          value: ""
        }, "- ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SELECT_OPTION'), " -"), this.props.selected_field.options.map(function (option) {
          return /*#__PURE__*/React.createElement("option", {
            key: option.value,
            value: option.value
          }, option.label);
        }));
      } else {
        return /*#__PURE__*/React.createElement("input", {
          type: "text",
          name: "arg",
          placeholder: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ENTER_VALUE'),
          onChange: this.props.onChange,
          value: this.props.value || '',
          className: "fieldValue"
        });
      }
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Rules.jsx":
/*!*****************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Rules.jsx ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Rules)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
/* harmony import */ var _Rule__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Rule */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Rule.jsx");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }


var Rules = /*#__PURE__*/function (_React$Component) {
  function Rules() {
    _classCallCheck(this, Rules);
    return _callSuper(this, Rules, arguments);
  }
  _inherits(Rules, _React$Component);
  return _createClass(Rules, [{
    key: "render",
    value: function render() {
      var _this = this;
      var rules = this.props.rules;
      var model = this.props.model(this.props.condition_id);
      return /*#__PURE__*/React.createElement("div", {
        className: "rules"
      }, Object.keys(rules).map(function (ruleGroupID, ruleIndex) {
        return /*#__PURE__*/React.createElement("div", {
          className: "ruleGroup",
          key: ruleGroupID,
          "data-label-or": Joomla.JText._('NR_OR')
        }, Object.keys(rules[ruleGroupID]).map(function (ruleID) {
          return /*#__PURE__*/React.createElement(_Rule__WEBPACK_IMPORTED_MODULE_1__["default"], {
            key: ruleID,
            rule: rules[ruleGroupID][ruleID],
            condition: _this.props.condition,
            form_fields: _this.props.form_fields,
            onChange: function onChange(e) {
              return model.update(e, ruleGroupID, ruleID);
            },
            onDelete: function onDelete() {
              return model["delete"](ruleGroupID, ruleID);
            }
          });
        }), /*#__PURE__*/React.createElement("button", {
          className: "cf-btn cf-icon-plus add-rule",
          title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ADD_RULE'),
          onClick: function onClick() {
            return model.add(ruleGroupID);
          }
        }), Object.keys(rules).length == ruleIndex + 1 && /*#__PURE__*/React.createElement("button", {
          style: {
            marginLeft: 3 + 'px'
          },
          className: "cf-btn cf-icon-plus add-rule-group",
          title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ADD_RULE_GROUP'),
          onClick: function onClick() {
            return model.addGroup(_this.props.condition_id);
          }
        }, " ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ADD_RULE_GROUP')));
      }), Object.keys(rules).length == 0 && /*#__PURE__*/React.createElement("button", {
        className: "cf-btn cf-icon-plus",
        title: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('ADD_RULE'),
        onClick: function onClick() {
          return model.addGroup(_this.props.condition_id);
        }
      }));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Trigger.jsx":
/*!*******************************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/Trigger.jsx ***!
  \*******************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Trigger)
/* harmony export */ });
/* harmony import */ var _Helper__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ../Helper */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js");
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function ownKeys(e, r) { var t = Object.keys(e); if (Object.getOwnPropertySymbols) { var o = Object.getOwnPropertySymbols(e); r && (o = o.filter(function (r) { return Object.getOwnPropertyDescriptor(e, r).enumerable; })), t.push.apply(t, o); } return t; }
function _objectSpread(e) { for (var r = 1; r < arguments.length; r++) { var t = null != arguments[r] ? arguments[r] : {}; r % 2 ? ownKeys(Object(t), !0).forEach(function (r) { _defineProperty(e, r, t[r]); }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) { Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r)); }); } return e; }
function _classCallCheck(a, n) { if (!(a instanceof n)) throw new TypeError("Cannot call a class as a function"); }
function _defineProperties(e, r) { for (var t = 0; t < r.length; t++) { var o = r[t]; o.enumerable = o.enumerable || !1, o.configurable = !0, "value" in o && (o.writable = !0), Object.defineProperty(e, _toPropertyKey(o.key), o); } }
function _createClass(e, r, t) { return r && _defineProperties(e.prototype, r), t && _defineProperties(e, t), Object.defineProperty(e, "prototype", { writable: !1 }), e; }
function _callSuper(t, o, e) { return o = _getPrototypeOf(o), _possibleConstructorReturn(t, _isNativeReflectConstruct() ? Reflect.construct(o, e || [], _getPrototypeOf(t).constructor) : o.apply(t, e)); }
function _possibleConstructorReturn(t, e) { if (e && ("object" == _typeof(e) || "function" == typeof e)) return e; if (void 0 !== e) throw new TypeError("Derived constructors may only return object or undefined"); return _assertThisInitialized(t); }
function _assertThisInitialized(e) { if (void 0 === e) throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); return e; }
function _isNativeReflectConstruct() { try { var t = !Boolean.prototype.valueOf.call(Reflect.construct(Boolean, [], function () {})); } catch (t) {} return (_isNativeReflectConstruct = function _isNativeReflectConstruct() { return !!t; })(); }
function _getPrototypeOf(t) { return _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf.bind() : function (t) { return t.__proto__ || Object.getPrototypeOf(t); }, _getPrototypeOf(t); }
function _inherits(t, e) { if ("function" != typeof e && null !== e) throw new TypeError("Super expression must either be null or a function"); t.prototype = Object.create(e && e.prototype, { constructor: { value: t, writable: !0, configurable: !0 } }), Object.defineProperty(t, "prototype", { writable: !1 }), e && _setPrototypeOf(t, e); }
function _setPrototypeOf(t, e) { return _setPrototypeOf = Object.setPrototypeOf ? Object.setPrototypeOf.bind() : function (t, e) { return t.__proto__ = e, t; }, _setPrototypeOf(t, e); }
function _defineProperty(e, r, t) { return (r = _toPropertyKey(r)) in e ? Object.defineProperty(e, r, { value: t, enumerable: !0, configurable: !0, writable: !0 }) : e[r] = t, e; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }

var Trigger = /*#__PURE__*/function (_React$Component) {
  function Trigger() {
    var _this;
    _classCallCheck(this, Trigger);
    for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
      args[_key] = arguments[_key];
    }
    _this = _callSuper(this, Trigger, [].concat(args));
    _defineProperty(_this, "getOptions", function () {
      if (!_this.props.selected_field) {
        return {};
      }
      var options = {
        show_field: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SHOW_FIELD'),
        hide_field: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('HIDE_FIELD')
      };
      if (_this.props.selected_field.hasInput) {
        options['copy_value'] = _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('COPY_VALUE');
      }
      var input_options = {
        change_value: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('CHANGE_VALUE')
      };
      var list_options = {
        select_option: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SELECT_OPTION'),
        deselect_option: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('DESELECT_OPTION'),
        show_option: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SHOW_OPTION'),
        show_all_options: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SHOW_ALL_OPTIONS'),
        hide_option: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('HIDE_OPTION'),
        hide_all_options: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('HIDE_ALL_OPTIONS'),
        filter_regex_option: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('FILTER_OPTIONS_REGEX'),
        show_regex_option: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SHOW_OPTIONS_REGEX'),
        hide_regex_option: _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('HIDE_OPTIONS_REGEX')
      };
      switch (_this.props.selected_field.type) {
        // We can't deselect a dropdown option
        case 'dropdown':
          delete list_options.deselect_option;
          break;
        case 'hidden':
          return input_options;
      }
      if (_this.props.selected_field.options) {
        return _objectSpread(_objectSpread({}, options), list_options);
      }
      if (_this.props.selected_field.name) {
        options = _objectSpread(_objectSpread({}, options), input_options);
      }
      return options;
    });
    return _this;
  }
  _inherits(Trigger, _React$Component);
  return _createClass(Trigger, [{
    key: "render",
    value: function render() {
      var options = this.getOptions();
      return /*#__PURE__*/React.createElement("select", {
        name: "trigger",
        onChange: this.props.onChange,
        value: this.props.value,
        className: "trigger"
      }, /*#__PURE__*/React.createElement("option", {
        value: ""
      }, "- ", _Helper__WEBPACK_IMPORTED_MODULE_0__["default"].text('SELECT_ACTION'), " -"), Object.keys(options).map(function (key) {
        return /*#__PURE__*/React.createElement("option", {
          key: key,
          value: key
        }, options[key]);
      }));
    }
  }]);
}(React.Component);


/***/ }),

/***/ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js":
/*!******************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Helper.js ***!
  \******************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
var Helper = {
  getRandomID: function getRandomID() {
    return Math.random().toString(36).substr(2, 9);
  },
  text: function text(language_string) {
    return Joomla.JText._('PLG_CONVERTFORMSTOOLS_CONDITIONALLOGIC_' + language_string);
  }
};
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (Helper);

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*****************************************************************************************!*\
  !*** ./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/index.js ***!
  \*****************************************************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _Components_AppFields__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Components/AppFields */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/AppFields.jsx");
/* harmony import */ var _Components_AppRules__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Components/AppRules */ "./source/media/plg_convertformstools_conditionallogic/js/src/app-builder/Components/AppRules.jsx");


document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.clstart').forEach(function (el) {
    el.addEventListener('click', function (e) {
      e.preventDefault();
      ReactDOM.render(/*#__PURE__*/React.createElement(_Components_AppFields__WEBPACK_IMPORTED_MODULE_0__["default"], {
        el: e.target.nextElementSibling
      }), document.getElementById('clb-root'));
    });
  });
  document.querySelectorAll('.clb').forEach(function (el) {
    ReactDOM.render(/*#__PURE__*/React.createElement(_Components_AppRules__WEBPACK_IMPORTED_MODULE_1__["default"], {
      el: el.querySelector('input')
    }), el.querySelector('div'));
  });
});
})();

/******/ })()
;
