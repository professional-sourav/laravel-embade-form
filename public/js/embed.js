/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!**************************************!*\
  !*** ./resources/js/embed-widget.js ***!
  \**************************************/
function _typeof(o) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) { return typeof o; } : function (o) { return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o; }, _typeof(o); }
function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }
function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, _toPropertyKey(descriptor.key), descriptor); } }
function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); Object.defineProperty(Constructor, "prototype", { writable: false }); return Constructor; }
function _toPropertyKey(t) { var i = _toPrimitive(t, "string"); return "symbol" == _typeof(i) ? i : i + ""; }
function _toPrimitive(t, r) { if ("object" != _typeof(t) || !t) return t; var e = t[Symbol.toPrimitive]; if (void 0 !== e) { var i = e.call(t, r || "default"); if ("object" != _typeof(i)) return i; throw new TypeError("@@toPrimitive must return a primitive value."); } return ("string" === r ? String : Number)(t); }
// embed-widget.js - Provide this to clients
(function () {
  var EmbedFormWidget = /*#__PURE__*/function () {
    function EmbedFormWidget(options) {
      _classCallCheck(this, EmbedFormWidget);
      this.container = options.container;
      this.formId = options.formId;
      this.apiUrl = options.apiUrl || 'https://testherd.test';
      this.onSuccess = options.onSuccess || function () {};
      this.init();
      console.log("API URL", this.apiUrl);
    }
    return _createClass(EmbedFormWidget, [{
      key: "init",
      value: function init() {
        var _this = this;
        var iframe = document.createElement('iframe');
        iframe.src = "".concat(this.apiUrl, "/embed/form?form_id=").concat(this.formId);
        iframe.style.width = '100%';
        iframe.style.border = 'none';
        iframe.style.overflow = 'hidden';
        iframe.setAttribute('scrolling', 'no');
        iframe.setAttribute('allow', 'same-origin');
        this.iframe = iframe;

        // Listen for messages from iframe
        window.addEventListener('message', function (event) {
          if (event.origin !== _this.apiUrl) return;
          switch (event.data.type) {
            case 'embedFormResize':
              iframe.style.height = event.data.height + 'px';
              break;
            case 'embedFormSuccess':
              _this.onSuccess(event.data.data);
              break;
          }
        });
        this.container.appendChild(iframe);
      }
    }]);
  }();
  window.EmbedFormWidget = EmbedFormWidget;
})();
/******/ })()
;