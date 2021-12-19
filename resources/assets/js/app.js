try {
    window.$ = window.jQuery = require('jquery');
} catch (e) {}

$(document).ready(() => {
    let activeLink = $('nav a[href^="/' + location.pathname.split("/")[1] + '"]');

    if (activeLink.length > 1) {
        return false;
    }

    activeLink.removeClass('text-gray-900 bg-white hover:bg-gray-200')
        .addClass('bg-gray-900 text-white');
});

$('nav a').click(() => {
    $('nav a')
        .removeClass('bg-gray-900 text-white')
        .addClass('text-gray-900 bg-white hover:bg-gray-200');
    $(this)
        .removeClass('text-gray-900 bg-white hover:bg-gray-200')
        .addClass('bg-gray-900 text-white');
});

$('#link-home').click(() => {
    $('nav a')
        .removeClass('bg-gray-900 text-white')
        .addClass('text-gray-900 bg-white hover:bg-gray-200');
});

const hljs = require('highlight.js');
const hljsDefineVue = require('highlightjs-vue');

hljsDefineVue(hljs);

document.querySelectorAll('pre > code').forEach(block => {
    hljs.highlightBlock(block);
});