/* GatewayOS2 — Crypto Playground */

function caesarCipher(text, shift, decrypt) {
  var s = decrypt ? (26 - (shift % 26)) : (shift % 26);
  var result = '';
  for (var i = 0; i < text.length; i++) {
    var c = text.charCodeAt(i);
    if (c >= 65 && c <= 90) {
      result += String.fromCharCode(((c - 65 + s) % 26) + 65);
    } else if (c >= 97 && c <= 122) {
      result += String.fromCharCode(((c - 97 + s) % 26) + 97);
    } else {
      result += text[i];
    }
  }
  return result;
}

function vigenereCipher(text, key, decrypt) {
  if (!key) return text;
  var k = key.toUpperCase();
  var result = '';
  var ki = 0;
  for (var i = 0; i < text.length; i++) {
    var c = text.charCodeAt(i);
    var shift = k.charCodeAt(ki % k.length) - 65;
    if (decrypt) shift = 26 - shift;
    if (c >= 65 && c <= 90) {
      result += String.fromCharCode(((c - 65 + shift) % 26) + 65);
      ki++;
    } else if (c >= 97 && c <= 122) {
      result += String.fromCharCode(((c - 97 + shift) % 26) + 97);
      ki++;
    } else {
      result += text[i];
    }
  }
  return result;
}

function xorCipher(text, key) {
  if (!key) return text;
  var result = '';
  for (var i = 0; i < text.length; i++) {
    var xored = text.charCodeAt(i) ^ key.charCodeAt(i % key.length);
    result += String.fromCharCode(xored);
  }
  return result;
}

function base64Encode(text) {
  try { return btoa(text); }
  catch (e) { return '[Error: invalid input]'; }
}

function base64Decode(text) {
  try { return atob(text); }
  catch (e) { return '[Error: invalid base64]'; }
}

function updateCrypto() {
  var algoSelect = document.getElementById('crypto-algo');
  var keyInput = document.getElementById('crypto-key');
  var inputArea = document.getElementById('crypto-input');
  var outputArea = document.getElementById('crypto-output');

  if (!algoSelect || !inputArea || !outputArea) return;

  var algo = algoSelect.value;
  var key = keyInput ? keyInput.value : '';
  var input = inputArea.value;
  var output = '';

  if (algo === 'caesar') {
    var shift = parseInt(key) || 3;
    output = caesarCipher(input, shift, false);
  } else if (algo === 'vigenere') {
    output = vigenereCipher(input, key || 'KEY', false);
  } else if (algo === 'xor') {
    var xored = xorCipher(input, key || 'K');
    output = '';
    for (var i = 0; i < xored.length; i++) {
      var hex = xored.charCodeAt(i).toString(16);
      output += (hex.length < 2 ? '0' : '') + hex + ' ';
    }
    output = output.trim();
  } else if (algo === 'base64') {
    output = base64Encode(input);
  }

  outputArea.value = output;
}

function decryptCrypto() {
  var algoSelect = document.getElementById('crypto-algo');
  var keyInput = document.getElementById('crypto-key');
  var inputArea = document.getElementById('crypto-input');
  var outputArea = document.getElementById('crypto-output');

  if (!algoSelect || !inputArea || !outputArea) return;

  var algo = algoSelect.value;
  var key = keyInput ? keyInput.value : '';
  var input = outputArea.value;
  var output = '';

  if (algo === 'caesar') {
    var shift = parseInt(key) || 3;
    output = caesarCipher(input, shift, true);
  } else if (algo === 'vigenere') {
    output = vigenereCipher(input, key || 'KEY', true);
  } else if (algo === 'xor') {
    var bytes = input.trim().split(/\s+/);
    var text = '';
    for (var i = 0; i < bytes.length; i++) {
      text += String.fromCharCode(parseInt(bytes[i], 16));
    }
    output = xorCipher(text, key || 'K');
  } else if (algo === 'base64') {
    output = base64Decode(input);
  }

  inputArea.value = output;
}

document.addEventListener('DOMContentLoaded', function() {
  var inputArea = document.getElementById('crypto-input');
  var algoSelect = document.getElementById('crypto-algo');
  var keyInput = document.getElementById('crypto-key');

  if (inputArea) {
    inputArea.addEventListener('input', updateCrypto);
  }
  if (algoSelect) {
    algoSelect.addEventListener('change', updateCrypto);
  }
  if (keyInput) {
    keyInput.addEventListener('input', updateCrypto);
  }
});
