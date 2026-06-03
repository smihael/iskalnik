document.addEventListener('alpine:init', function () {
    Alpine.data('detailsApp', function () {
        return {
            loading: true,
            errorMessage: '',
            title: 'Podrobnosti',
            collectionTitle: '',
            row: null,
            fields: {},
            links: {},
            credits: '',
            baseUrl: '',
            permalink: '',
            backUrl: './',
            currentCollection: '',
            generatedAt: null,
            basePath: (window.IPZ_ROUTE && window.IPZ_ROUTE.basePath) ? window.IPZ_ROUTE.basePath : '',

            get formattedGeneratedAt() {
                if (!this.generatedAt) {
                    return '';
                }
                var d = this.generatedAt;
                var dateStr = d.toLocaleDateString('sl-SI');
                var timeStr = d.toLocaleTimeString('sl-SI', { hour: '2-digit', minute: '2-digit' });
                return dateStr + ' ob ' + timeStr;
            },

            get fieldNames() {
                return Object.keys(this.fields || {});
            },

            resolvePrettyRoute: function () {
                var parts = window.location.pathname.split('/').filter(function (part) {
                    return part !== '';
                });

                if (parts.length < 2) {
                    return {};
                }

                var id = parts[parts.length - 1];
                var collection = parts[parts.length - 2];
                if (!/^[0-9]+$/.test(id) || collection === 'details.php') {
                    return {};
                }

                return {
                    collection: decodeURIComponent(collection),
                    id: decodeURIComponent(id),
                    basePath: parts.length > 2 ? '/' + parts.slice(0, -2).join('/') : ''
                };
            },

            init: async function () {
                try {
                    var params = new URLSearchParams(window.location.search);
                    var route = window.IPZ_ROUTE || {};
                    var prettyRoute = this.resolvePrettyRoute();
                    var collection = params.get('collection') || route.collection || prettyRoute.collection;
                    var id = params.get('id') || route.id || prettyRoute.id;
                    if (prettyRoute.basePath) {
                        this.basePath = prettyRoute.basePath;
                    }
                    if (!collection || !id) {
                        throw new Error('Manjka collection ali id parameter.');
                    }

                    this.permalink = new URL((this.basePath || '') + '/' + encodeURIComponent(collection) + '/' + encodeURIComponent(String(id)), window.location.origin).href;
                    this.backUrl = (this.basePath || '') + '/' + encodeURIComponent(collection) + '#results';
                    this.currentCollection = collection;
                    this.generatedAt = new Date();

                    var response = await fetch((this.basePath || '') + '/api/details.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ collection: collection, id: id })
                    });

                    var data = await response.json();
                    if (!data.ok) {
                        throw new Error(data.error || 'Branje podrobnosti ni uspelo.');
                    }

                    this.title = (data.meta && data.meta.title) ? data.meta.title + ' | zapis #' + id : 'Podrobnosti';
                    this.collectionTitle = (data.meta && data.meta.title) ? data.meta.title : 'Podrobnosti';
                    this.row = data.row || {};
                    this.fields = (data.meta && data.meta.fields) ? data.meta.fields : {};
                    this.links = (data.meta && data.meta.links) ? data.meta.links : {};
                    this.credits = (data.meta && data.meta.credits) ? data.meta.credits : '';
                    this.baseUrl = (data.meta && data.meta.base_url) ? data.meta.base_url : '';
                } catch (error) {
                    this.errorMessage = error.message || 'Napaka pri podrobnostih.';
                } finally {
                    this.loading = false;
                }
            },

            fieldLabel: function (field) {
                return this.fields[field] && this.fields[field].label ? this.fields[field].label : field;
            },

            escapeHtml: function (value) {
                return String(value)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            },

            splitList: function (value) {
                return String(value).split(/[,;|]/).map(function (chunk) {
                    return chunk.trim();
                }).filter(function (chunk) {
                    return chunk !== '';
                });
            },

            splitListParts: function (value) {
                var parts = String(value).split(/([,;|])/);
                var items = [];
                var current = null;

                parts.forEach(function (part) {
                    if (part === '') {
                        return;
                    }

                    if (/^[,;|]$/.test(part)) {
                        if (current) {
                            current.separator = part;
                        }
                        return;
                    }

                    var text = part.trim();
                    if (text !== '') {
                        current = { text: text, separator: '' };
                        items.push(current);
                    }
                });

                return items;
            },

            filterUrl: function (field, value) {
                var collection = this.currentCollection || (window.IPZ_ROUTE && window.IPZ_ROUTE.collection) || '';
                if (!collection) {
                    return (this.basePath || '') + '/';
                }

                var params = new URLSearchParams();
                params.set('filters', JSON.stringify([{
                    field: field,
                    operator: 'like',
                    value: String(value || '').trim()
                }]));

                params.set('collection', collection);
                return (this.basePath || '') + '/index.php?' + params.toString() + '#results';
            },

            resolveExternalUrl: function (field, value) {
                var mask = this.links[field];
                if (!mask) {
                    return null;
                }
                if (mask === '{value}') {
                    return value;
                }
                return mask.replace('{value}', encodeURIComponent(value));
            },

            renderCell: function (field, value) {
                if (value === null || value === undefined || value === '') {
                    return '';
                }

                var fieldMeta = this.fields[field] || { type: 'string' };
                var type = fieldMeta.type || 'string';
                var safeValue = this.escapeHtml(value);

                var external = this.resolveExternalUrl(field, value);
                if (external) {
                    return '<a class="external-link" target="_blank" rel="noopener" href="' + this.escapeHtml(external) + '">' + safeValue + '</a>';
                }

                if (type === 'url') {
                    return '<a class="external-link" target="_blank" rel="noopener" href="' + safeValue + '">' + safeValue + '</a>';
                }

                if (type === 'cobiss') {
                    var cobiss = 'https://plus.cobiss.net/cobiss/si/sl/bib/' + encodeURIComponent(String(value));
                    return '<a class="external-link" target="_blank" rel="noopener" href="' + this.escapeHtml(cobiss) + '">' + safeValue + '</a>';
                }

                if (type === 'clickable') {
                    return '<a class="chip" href="' + this.escapeHtml(this.filterUrl(field, value)) + '">' + safeValue + '</a>';
                }

                if (type === 'list') {
                    return this.splitListParts(value).map(function (item) {
                        var safeItem = this.escapeHtml(item.text);
                        var chip = '<a class="chip" href="' + this.escapeHtml(this.filterUrl(field, item.text)) + '">' + safeItem + '</a>';
                        if (item.separator) {
                            chip += '<span class="chip-separator">' + this.escapeHtml(item.separator) + '</span>';
                        }
                        return chip;
                    }.bind(this)).join('');
                }

                return safeValue;
            }
        };
    });
});
