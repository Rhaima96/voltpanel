<template>
    <div class="form-field" :class="columnSpanClass">
        <!-- Label (for fields that need it above the input) -->
        <label
            v-if="component.label && !['checkbox', 'toggle'].includes(component.type)"
            :for="component.name"
            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1"
        >
            {{ component.label }}
            <span v-if="component.required" class="text-red-500">*</span>
        </label>

        <!-- Text Input -->
        <TextInput
            v-if="component.type === 'text-input'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Textarea -->
        <TextArea
            v-else-if="component.type === 'textarea'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Select (Single) -->
        <SelectField
            v-else-if="component.type === 'select' && !component.multiple"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Select (Multiple with Tags) -->
        <MultiSelectField
            v-else-if="component.type === 'select' && component.multiple"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Toggle -->
        <Toggle
            v-else-if="component.type === 'toggle'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Checkbox -->
        <Checkbox
            v-else-if="component.type === 'checkbox'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Radio -->
        <RadioGroup
            v-else-if="component.type === 'radio'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Date Picker -->
        <DatePicker
            v-else-if="component.type === 'date-picker'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- DateTime Picker -->
        <DateTimePicker
            v-else-if="component.type === 'date-time-picker'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Color Picker -->
        <ColorPicker
            v-else-if="component.type === 'color-picker'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Rich Editor -->
        <RichEditor
            v-else-if="component.type === 'rich-editor'"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
            :fileAttachments="component.fileAttachments || false"
        />

        <!-- File Upload -->
        <FileUpload
            v-else-if="component.type === 'file-upload'"
            :component="component"
            :modelValue="modelValue"
            @update:modelValue="$emit('update:modelValue', $event)"
        />

        <!-- Comments -->
        <Comments
            v-else-if="component.type === 'comments'"
            :commentable-type="commentableType"
            :commentable-id="commentableId"
            :initial-comments="initialComments"
        />

        <!-- Helper Text -->
        <p
            v-if="component.helperText"
            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
        >
            {{ component.helperText }}
        </p>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import TextInput from './FormFields/TextInput.vue';
import TextArea from './FormFields/TextArea.vue';
import SelectField from './FormFields/SelectField.vue';
import MultiSelectField from './FormFields/MultiSelectField.vue';
import Toggle from './FormFields/Toggle.vue';
import Checkbox from './FormFields/Checkbox.vue';
import RadioGroup from './FormFields/RadioGroup.vue';
import DatePicker from './FormFields/DatePicker.vue';
import DateTimePicker from './FormFields/DateTimePicker.vue';
import ColorPicker from './FormFields/ColorPicker.vue';
import FileUpload from './FormFields/FileUpload.vue';
import RichEditor from './RichEditor.vue';
import Comments from './Comments.vue';

const props = defineProps({
    component: {
        type: Object,
        required: true,
    },
    modelValue: {
        type: [String, Number, Boolean, Array, File],
        default: null,
    },
    commentableType: {
        type: String,
        default: null,
    },
    commentableId: {
        type: Number,
        default: null,
    },
    initialComments: {
        type: Array,
        default: () => [],
    },
});

defineEmits(['update:modelValue']);

// Map columnSpan to Tailwind classes (explicit for Tailwind purging)
const columnSpanClass = computed(() => {
    const span = props.component.columnSpan || 12;
    const spanMap = {
        1: 'col-span-1',
        2: 'col-span-2',
        3: 'col-span-3',
        4: 'col-span-4',
        5: 'col-span-5',
        6: 'col-span-6',
        7: 'col-span-7',
        8: 'col-span-8',
        9: 'col-span-9',
        10: 'col-span-10',
        11: 'col-span-11',
        12: 'col-span-12',
    };
    return spanMap[span] || 'col-span-12';
});
</script>
