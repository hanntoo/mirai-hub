@once
<script>
    function uid() {
        if (typeof crypto !== 'undefined' && typeof crypto.randomUUID === 'function') {
            return crypto.randomUUID();
        }
        return Math.random().toString(36).slice(2) + Date.now().toString(36);
    }

    function handleBannerUpload(event, field) {
        const file = event.target.files?.[0];
        if (!file) return;
        if (file.size > 800 * 1024) {
            alert('Ukuran file terlalu besar! Maksimal 800KB.');
            event.target.value = null;
            return;
        }
        const reader = new FileReader();
        reader.onload = () => {
            field.content = reader.result;
        };
        reader.readAsDataURL(file);
    }

    function builderState(initialFields = [], wire, propertyName = 'fields') {
        return {
            fields: Array.isArray(initialFields) && initialFields.length ? JSON.parse(JSON.stringify(initialFields)) : [],
            propertyName: propertyName,

            addField(type = 'text') {
                const field = {
                    id: uid(),
                    type,
                    label: '',
                    required: false,
                    description: '',
                    imageUrl: '',
                    linkUrl: '',
                    linkText: '',
                    showImageInput: false,
                    showLinkInput: false,
                };

                if (['note', 'image-view', 'link'].includes(type)) {
                    field.label = type === 'note' ? 'Judul Baru' : (type === 'link' ? 'Judul Link' : 'Judul Gambar');
                    field.content = '';
                }

                if (type === 'section') {
                    field.label = 'Bagian Baru';
                    field.sectionTitle = 'Bagian Baru';
                }

                if (['select', 'radio', 'checkbox'].includes(type)) {
                    field.options = ['Opsi 1', 'Opsi 2'];
                }

                if (type === 'file') {
                    field.fileSettings = {
                        allowedTypes: ['image', 'pdf'],
                        maxSize: 1048576,
                        restrictTypes: false,
                    };
                }

                if (!field.label && ['text', 'textarea', 'radio', 'checkbox', 'select', 'file', 'date', 'time', 'datetime-local'].includes(type)) {
                    field.label = 'Pertanyaan';
                }

                this.fields.push(field);
            },

            removeField(index) {
                this.fields.splice(index, 1);
            },

            moveFieldUp(index) {
                if (index <= 0) return;
                const [item] = this.fields.splice(index, 1);
                this.fields.splice(index - 1, 0, item);
            },

            moveFieldDown(index) {
                if (index >= this.fields.length - 1) return;
                const [item] = this.fields.splice(index, 1);
                this.fields.splice(index + 1, 0, item);
            },

            duplicateField(index) {
                const original = this.fields[index];
                if (!original) return;
                const duplicate = JSON.parse(JSON.stringify(original));
                duplicate.id = uid();
                duplicate.label = (duplicate.label || '') + ' (Copy)';
                this.fields.splice(index + 1, 0, duplicate);
            },

            updateFieldType(index, type) {
                const field = this.fields[index];
                if (!field) return;

                field.type = type;

                if (['select', 'radio', 'checkbox'].includes(type)) {
                    field.options = field.options?.length ? field.options : ['Opsi 1', 'Opsi 2'];
                } else {
                    delete field.options;
                }

                if (type === 'file') {
                    field.fileSettings = field.fileSettings || {
                        allowedTypes: ['image', 'pdf'],
                        maxSize: 1048576,
                        restrictTypes: false,
                    };
                } else {
                    delete field.fileSettings;
                }

                if (['note', 'image-view', 'link'].includes(type)) {
                    field.content = field.content || '';
                } else {
                    delete field.content;
                }

                if (type === 'section') {
                    field.sectionTitle = field.sectionTitle || 'Bagian Baru';
                } else {
                    delete field.sectionTitle;
                }
            },

            addOption(fieldIndex) {
                const field = this.fields[fieldIndex];
                if (!field) return;
                field.options = field.options || [];
                field.options.push('');
            },

            removeOption(fieldIndex, optionIndex) {
                const field = this.fields[fieldIndex];
                if (!field?.options) return;
                field.options.splice(optionIndex, 1);
            },

            toggleRequired(field) {
                field.required = !field.required;
            },

            async syncAndSave() {
                await wire.set(this.propertyName, this.fields, false);
                await wire.call('save');
            },
        };
    }

    function mediaEmbedder(field) {
        return {
            showImage: field.showImageInput || false,
            showLink: field.showLinkInput || false,
            showEmbedFullscreen: false,
            uploading: false,
            imageUrl: field.imageUrl || '',

            toggleImage() {
                this.showImage = !this.showImage;
                field.showImageInput = this.showImage;
            },

            toggleLink() {
                this.showLink = !this.showLink;
                field.showLinkInput = this.showLink;
            },

            clearImageData() {
                this.imageUrl = '';
                field.imageUrl = '';
                this.showImage = false;
                field.showImageInput = false;
            },

            clearLinkData() {
                this.showLink = false;
                field.linkUrl = '';
                field.linkText = '';
                field.showLinkInput = false;
            },

            handleImageUpload(event) {
                const file = event.target.files?.[0];
                if (!file) return;
                if (file.size > 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 1MB.');
                    event.target.value = null;
                    return;
                }
                this.uploading = true;
                const reader = new FileReader();
                reader.onload = () => {
                    this.imageUrl = reader.result;
                    field.imageUrl = reader.result;
                    this.uploading = false;
                };
                reader.readAsDataURL(file);
            },
        };
    }

    function fileSettings(field) {
        return {
            fileTypeOptions: [
                { id: 'image', label: 'Gambar' },
                { id: 'pdf', label: 'PDF' },
                { id: 'document', label: 'Dokumen' },
                { id: 'video', label: 'Video' },
            ],

            get restrictTypes() {
                return field.fileSettings?.restrictTypes || false;
            },

            set restrictTypes(val) {
                if (!field.fileSettings) {
                    field.fileSettings = { allowedTypes: ['image', 'pdf'], maxSize: 1048576, restrictTypes: val };
                } else {
                    field.fileSettings.restrictTypes = val;
                }
            },

            get allowedTypes() {
                return field.fileSettings?.allowedTypes || [];
            },

            toggleRestrict() {
                this.restrictTypes = !this.restrictTypes;
            },

            toggleType(type) {
                const types = [...this.allowedTypes];
                const exists = types.indexOf(type);
                if (exists >= 0) {
                    types.splice(exists, 1);
                } else {
                    types.push(type);
                }
                field.fileSettings = field.fileSettings || { allowedTypes: [], maxSize: 1048576, restrictTypes: false };
                field.fileSettings.allowedTypes = types;
            },
        };
    }
</script>
@endonce
