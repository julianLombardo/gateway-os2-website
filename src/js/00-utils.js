/* GatewayOS2 — DOM Utilities */

function $(selector) { return document.querySelector(selector); }
function $$(selector) { return document.querySelectorAll(selector); }
function debounce(fn, ms) { var t; return function() { clearTimeout(t); t = setTimeout(fn.bind(this, ...arguments), ms); }; }
function fetchJSON(url, opts) { return fetch(url, opts).then(function(r) { return r.json(); }); }
