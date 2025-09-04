import "quill/dist/quill.snow.css";

import Quill from 'quill';

const editor = new Quill('#editor', {
    modules: {
        toolbar: [
            ['bold', 'italic'],
            ['link', 'blockquote', 'code-block', 'image'],
            [{ list: 'ordered' }, { list: 'bullet' }],
        ],
    },
    theme: 'snow',
});

const form = document.querySelector('form');
form.addEventListener('formdata', (event) => {
    // Append Quill content before submitting
    // event.formData.append('content', JSON.stringify(editor.getSemanticHTML()));
    event.formData.append('content', editor.getSemanticHTML());
});
