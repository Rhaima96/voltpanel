<template>
    <div class="rich-editor-wrapper">
        <div v-if="editor" class="border border-gray-300 dark:border-gray-600 rounded-md overflow-hidden bg-white dark:bg-gray-800">
            <!-- Toolbar -->
            <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600 p-2 flex items-center flex-wrap gap-1">
                <!-- Text Formatting -->
                <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-2">
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleBold().run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('bold') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Bold"
                    >
                        <strong class="text-sm">B</strong>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleItalic().run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('italic') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Italic"
                    >
                        <em class="text-sm">I</em>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleUnderline().run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('underline') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Underline"
                    >
                        <span class="text-sm underline">U</span>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleStrike().run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('strike') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Strikethrough"
                    >
                        <span class="text-sm line-through">S</span>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().toggleHighlight().run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('highlight') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Highlight"
                    >
                        <span class="text-sm bg-yellow-200 dark:bg-yellow-600 px-1">H</span>
                    </button>
                </div>

                <!-- Text Color -->
                <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-2">
                    <input
                        type="color"
                        @input="editor.chain().focus().setColor($event.target.value).run()"
                        :value="editor.getAttributes('textStyle').color || '#000000'"
                        class="w-8 h-8 rounded cursor-pointer"
                        title="Text Color"
                    />
                </div>

                <!-- Headings -->
                <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-2">
                    <button
                        type="button"
                        @click="toggleHeading(1)"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('heading', { level: 1 }) }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded text-sm transition-colors"
                        title="Heading 1"
                    >
                        H1
                    </button>
                    <button
                        type="button"
                        @click="toggleHeading(2)"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('heading', { level: 2 }) }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded text-sm transition-colors"
                        title="Heading 2"
                    >
                        H2
                    </button>
                    <button
                        type="button"
                        @click="toggleHeading(3)"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('heading', { level: 3 }) }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded text-sm transition-colors"
                        title="Heading 3"
                    >
                        H3
                    </button>
                </div>

                <!-- Text Alignment -->
                <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-2">
                    <button
                        type="button"
                        @click="editor.chain().focus().setTextAlign('left').run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive({ textAlign: 'left' }) }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Align Left"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4h14v2H3V4zm0 4h10v2H3V8zm0 4h14v2H3v-2zm0 4h10v2H3v-2z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().setTextAlign('center').run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive({ textAlign: 'center' }) }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Align Center"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4h14v2H3V4zm2 4h10v2H5V8zm-2 4h14v2H3v-2zm2 4h10v2H5v-2z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().setTextAlign('right').run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive({ textAlign: 'right' }) }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Align Right"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4h14v2H3V4zm4 4h10v2H7V8zm-4 4h14v2H3v-2zm4 4h10v2H7v-2z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().setTextAlign('justify').run()"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive({ textAlign: 'justify' }) }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Justify"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4h14v2H3V4zm0 4h14v2H3V8zm0 4h14v2H3v-2zm0 4h14v2H3v-2z"/>
                        </svg>
                    </button>
                </div>

                <!-- Lists -->
                <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-2">
                    <button
                        type="button"
                        @click="toggleBulletList"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('bulletList') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Bullet List"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4h2v2H3V4zm4 0h10v2H7V4zM3 8h2v2H3V8zm4 0h10v2H7V8zm-4 4h2v2H3v-2zm4 0h10v2H7v-2z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="toggleOrderedList"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('orderedList') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Numbered List"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M4 4h1v4H4V4zm3 0h10v2H7V4zm-3 6h1v4H4v-4zm3 0h10v2H7v-2z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="toggleBlockquote"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('blockquote') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Quote"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M6 10c0-2 1.5-3.5 3.5-3.5S13 8 13 10c0 1.5-1 2.5-2 3l-1 1v1H8v-2l1.5-1.5c.5-.5 1-1 1-1.5 0-.5-.5-1-1-1s-1 .5-1 1H6zm7 0c0-2 1.5-3.5 3.5-3.5S20 8 20 10c0 1.5-1 2.5-2 3l-1 1v1h-2v-2l1.5-1.5c.5-.5 1-1 1-1.5 0-.5-.5-1-1-1s-1 .5-1 1h-2z"/>
                        </svg>
                    </button>
                </div>

                <!-- Link -->
                <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-2">
                    <button
                        type="button"
                        @click="addLink"
                        :class="{ 'bg-gray-300 dark:bg-gray-600': editor.isActive('link') }"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Add Link"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z"/>
                        </svg>
                    </button>
                </div>

                <!-- Media -->
                <div class="flex items-center gap-1 border-r border-gray-300 dark:border-gray-600 pr-2 mr-2">
                    <button
                        type="button"
                        @click="triggerImageUpload"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Insert Image"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="addYouTube"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Embed YouTube"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 0C4.477 0 0 4.477 0 10s4.477 10 10 10 10-4.477 10-10S15.523 0 10 0zm3.5 10L8 13V7l5.5 3z"/>
                        </svg>
                    </button>
                    <button
                        v-if="fileAttachments"
                        type="button"
                        @click="triggerFileUpload"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors"
                        title="Attach File"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a3 3 0 00-3 3v4a5 5 0 0010 0V7a1 1 0 112 0v4a7 7 0 11-14 0V7a5 5 0 0110 0v4a3 3 0 11-6 0V7a1 1 0 012 0v4a1 1 0 102 0V7a3 3 0 00-3-3z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>

                <!-- Utilities -->
                <div class="flex items-center gap-1">
                    <button
                        type="button"
                        @click="editor.chain().focus().undo().run()"
                        :disabled="!editor.can().undo()"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors disabled:opacity-50"
                        title="Undo"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8 5H5v3L1 4l4-4v3h3a7 7 0 017 7 1 1 0 11-2 0 5 5 0 00-5-5z"/>
                        </svg>
                    </button>
                    <button
                        type="button"
                        @click="editor.chain().focus().redo().run()"
                        :disabled="!editor.can().redo()"
                        class="p-2 hover:bg-gray-200 dark:hover:bg-gray-600 rounded transition-colors disabled:opacity-50"
                        title="Redo"
                    >
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M12 5h3v3l4-4-4-4v3h-3a7 7 0 00-7 7 1 1 0 102 0 5 5 0 015-5z"/>
                        </svg>
                    </button>
                </div>

                <!-- Hidden file inputs -->
                <input
                    ref="imageInput"
                    type="file"
                    accept="image/*"
                    @change="handleImageUpload"
                    class="hidden"
                />
                <input
                    v-if="fileAttachments"
                    ref="fileInput"
                    type="file"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,.rar"
                    @change="handleFileUpload"
                    class="hidden"
                />
            </div>

            <!-- Editor Content -->
            <editor-content :editor="editor" class="prose prose-sm dark:prose-invert max-w-none" />
        </div>
    </div>
</template>

<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';
import { useEditor, EditorContent } from '@tiptap/vue-3';
import StarterKit from '@tiptap/starter-kit';
import Highlight from '@tiptap/extension-highlight';
import Underline from '@tiptap/extension-underline';
import Link from '@tiptap/extension-link';
import TextAlign from '@tiptap/extension-text-align';
import ImageResize from 'tiptap-extension-resize-image';
import YouTube from '@tiptap/extension-youtube';
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';
import { useRouting } from '../../composables/VoltPanel/useRouting';

const { route } = useRouting();

const props = defineProps({
    modelValue: {
        type: String,
        default: '',
    },
    fileAttachments: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['update:modelValue']);

const imageInput = ref(null);
const fileInput = ref(null);

// Initialize editor
const editor = useEditor({
    extensions: [
        StarterKit.configure({
            heading: {
                levels: [1, 2, 3],
            },
        }),
        Highlight,
        Underline,
        Link.configure({
            openOnClick: false,
            HTMLAttributes: {
                class: 'text-primary-600 dark:text-primary-400 hover:underline',
            },
        }),
        TextAlign.configure({
            types: ['heading', 'paragraph'],
        }),
        ImageResize.configure({
            inline: false,
            allowBase64: true,
        }),
        YouTube.configure({
            width: 640,
            height: 360,
        }),
        TextStyle,
        Color,
    ],
    content: props.modelValue || '',
    editorProps: {
        attributes: {
            class: 'prose prose-sm dark:prose-invert max-w-none focus:outline-none min-h-[300px] p-4',
        },
    },
    onUpdate: ({ editor }) => {
        emit('update:modelValue', editor.getHTML());
    },
});

// Watch for external changes
watch(() => props.modelValue, (newValue) => {
    if (editor.value && newValue && editor.value.getHTML() !== newValue) {
        editor.value.commands.setContent(newValue);
    }
});

// Helper to get CSRF token from cookie
const getCsrfToken = () => {
    const name = 'XSRF-TOKEN';
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
        return decodeURIComponent(parts.pop().split(';').shift());
    }
    return '';
};

// Add link
const addLink = () => {
    const previousUrl = editor.value.getAttributes('link').href;
    const url = window.prompt('Enter URL:', previousUrl);

    if (url === null) return;

    if (url === '') {
        editor.value.chain().focus().extendMarkRange('link').unsetLink().run();
        return;
    }

    editor.value.chain().focus().extendMarkRange('link').setLink({ href: url }).run();
};

// Add YouTube
const addYouTube = () => {
    const url = window.prompt('Enter YouTube URL:');

    if (url) {
        editor.value.chain().focus().setYoutubeVideo({ src: url }).run();
    }
};

// Toggle headings
const toggleHeading = (level) => {
    if (!editor.value) return;
    editor.value.chain().focus().toggleHeading({ level }).run();
};

// Toggle bullet list
const toggleBulletList = () => {
    if (!editor.value) return;
    editor.value.chain().focus().toggleBulletList().run();
};

// Toggle ordered list
const toggleOrderedList = () => {
    if (!editor.value) return;
    editor.value.chain().focus().toggleOrderedList().run();
};

// Toggle blockquote
const toggleBlockquote = () => {
    if (!editor.value) return;
    editor.value.chain().focus().toggleBlockquote().run();
};

// Image upload
const triggerImageUpload = () => {
    imageInput.value?.click();
};

const handleImageUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    // Get file extension, icon and size
    const extension = file.name.split('.').pop().toLowerCase();
    const fileSize = formatFileSize(file.size);

    // Save current cursor position
    const { from } = editor.value.state.selection;

    try {
        // Resize image if needed
        const img = new Image();
        const reader = new FileReader();

        img.onload = () => {
            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            const maxWidth = 1200;
            const maxHeight = 1200;

            let width = img.width;
            let height = img.height;

            if (width > maxWidth || height > maxHeight) {
                const ratio = Math.min(maxWidth / width, maxHeight / height);
                width = Math.round(width * ratio);
                height = Math.round(height * ratio);
            }

            canvas.width = width;
            canvas.height = height;

            const isPng = file.type === 'image/png';
            const outputFormat = isPng ? 'image/png' : 'image/jpeg';
            const quality = isPng ? 0.95 : 0.85;

            if (!isPng) {
                ctx.fillStyle = '#FFFFFF';
                ctx.fillRect(0, 0, width, height);
            }

            ctx.drawImage(img, 0, 0, width, height);
            const resizedBase64 = canvas.toDataURL(outputFormat, quality);

            editor.value.chain().focus().setImage({ src: resizedBase64 }).run();
            event.target.value = '';
        };

        reader.onload = (e) => {
            img.src = e.target.result;
        };

        reader.readAsDataURL(file);
    } catch (error) {
        console.error('Image upload failed:', error);
        alert('Failed to upload image. Please try again.');
    }
};

// File upload
const triggerFileUpload = () => {
    fileInput.value?.click();
};

const handleFileUpload = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const extension = file.name.split('.').pop().toLowerCase();
    const fileIcon = getFileIcon(extension);
    const fileSize = formatFileSize(file.size);
    const { from } = editor.value.state.selection;

    try {
        const formData = new FormData();
        formData.append('file', file);

        const response = await fetch(route('voltpanel.api.rich-editor.upload'), {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': getCsrfToken()
            }
        });

        if (!response.ok) {
            throw new Error('Upload failed');
        }

        const data = await response.json();

        const fileHtml = `<p style="background-color: #f3f4f6; padding: 12px; border-radius: 8px; border-left: 4px solid #3b82f6; margin: 8px 0;"><a href="${data.url}" download="${data.filename}" class="file-attachment-link" style="display: block; text-decoration: none; color: inherit; cursor: pointer;"><strong>${fileIcon} ${data.filename}</strong><br><span style="font-size: 0.875em; color: #6b7280;">${fileSize} • Click to download</span></a></p>`;

        editor.value.chain().focus().insertContentAt(from, fileHtml).run();
    } catch (error) {
        console.error('File upload failed:', error);
        alert('Failed to upload file. Please try again.');
    }

    event.target.value = '';
};

const getFileIcon = (extension) => {
    const icons = {
        'pdf': '📄', 'doc': '📝', 'docx': '📝',
        'xls': '📊', 'xlsx': '📊',
        'ppt': '📽️', 'pptx': '📽️',
        'txt': '📃', 'csv': '📊',
        'zip': '🗜️', 'rar': '🗜️',
    };
    return icons[extension] || '📎';
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
};

onBeforeUnmount(() => {
    if (editor.value) {
        editor.value.destroy();
    }
});
</script>

<style scoped>
:deep(.ProseMirror) {
    min-height: 300px;
    padding: 1rem;
    outline: none;
}

/* Headings */
:deep(.ProseMirror h1) {
    font-size: 2em;
    font-weight: 700;
    margin-top: 0.67em;
    margin-bottom: 0.67em;
    line-height: 1.2;
}

:deep(.ProseMirror h2) {
    font-size: 1.5em;
    font-weight: 600;
    margin-top: 0.83em;
    margin-bottom: 0.83em;
    line-height: 1.3;
}

:deep(.ProseMirror h3) {
    font-size: 1.17em;
    font-weight: 600;
    margin-top: 1em;
    margin-bottom: 1em;
    line-height: 1.4;
}

/* Lists */
:deep(.ProseMirror ul) {
    list-style-type: disc;
    padding-left: 1.5em;
    margin: 1em 0;
}

:deep(.ProseMirror ol) {
    list-style-type: decimal;
    padding-left: 1.5em;
    margin: 1em 0;
}

:deep(.ProseMirror li) {
    margin: 0.5em 0;
}

/* Blockquote */
:deep(.ProseMirror blockquote) {
    border-left: 4px solid #e5e7eb;
    padding-left: 1em;
    margin: 1em 0;
    font-style: italic;
    color: #6b7280;
}

:deep(.dark .ProseMirror blockquote) {
    border-left-color: #4b5563;
    color: #9ca3af;
}

/* Paragraph */
:deep(.ProseMirror p) {
    margin: 0.5em 0;
}

:deep(.ProseMirror img) {
    max-width: 100%;
    height: auto;
    border-radius: 0.5rem;
    display: inline-block;
}

:deep(.ProseMirror img.ProseMirror-selectednode) {
    outline: 2px solid #3b82f6;
    outline-offset: 2px;
}

:deep(.ProseMirror iframe) {
    border-radius: 0.5rem;
    max-width: 100%;
}

:deep(.ProseMirror .file-attachment-link) {
    display: block;
    text-decoration: none;
    color: inherit;
    cursor: pointer;
    transition: opacity 0.2s;
}

:deep(.ProseMirror .file-attachment-link:hover) {
    opacity: 0.8;
}

:deep(.ProseMirror p:has(.file-attachment-link)) {
    background-color: #f3f4f6;
    padding: 12px;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
    margin: 8px 0;
}

:deep(.dark .ProseMirror p:has(.file-attachment-link)) {
    background-color: #374151 !important;
    border-color: #60a5fa !important;
}

:deep(.dark .ProseMirror .file-attachment-link span[style*="color: #6b7280"]) {
    color: #9ca3af !important;
}
</style>
