import 'quill/dist/quill.snow.css'
import Quill from 'quill'

interface FormDataEvent extends Event {
    readonly formData: FormData
}

const editor = new Quill('#editor', {
    modules: {
        toolbar: [
            ['bold', 'italic'],
            ['link', 'blockquote', 'code-block', 'image'],
            [{ list: 'ordered' }, { list: 'bullet' }],
        ],
    },
    theme: 'snow',
})

const form = document.querySelector('form')
if (form) {
    // Append Quill content before submitting
    form.addEventListener('formdata', (event: FormDataEvent) => {
        event.formData.append('content', editor.getSemanticHTML())

        const modelSelect = document.getElementById('currentMenu') as HTMLInputElement | null
        if (modelSelect && modelSelect.textContent) {
            event.formData.append('model', modelSelect.textContent)
        }
    })
}
