import "quill/dist/quill.core.css";
import "quill/dist/snow.css";

import Quill from 'quill';

// document.addEventListener('DOMContentLoaded', function() {
//     if (document.getElementById('quill-editor-area')) {
//         var editor = new Quill('#quill-editor', {
//             theme: 'snow'
//         });
//         var quillEditor = document.getElementById('quill-editor-area');
//         editor.on('text-change', function() {
//             quillEditor.value = editor.root.innerHTML;
//         });
//
//         quillEditor.addEventListener('input', function() {
//             editor.root.innerHTML = quillEditor.value;
//         });
//     }
// });


const quill = new Quill('#editor', {
    // modules: {
    //     toolbar: [
    //         ['bold', 'italic'],
    //         ['link', 'blockquote', 'code-block', 'image'],
    //         [{ list: 'ordered' }, { list: 'bullet' }],
    //     ],
    // },
    theme: 'snow',
});

const form = document.querySelector('form');
form.addEventListener('formdata', (event) => {
    // Append Quill content before submitting
    event.formData.append('about', JSON.stringify(quill.getContents().ops));
});

