document.addEventListener('alpine:init', function () {
    Alpine.data('searchApp', function () {
        return {
            operators: ['like', 'not like', '=', '<>', '>', '<', '>=', '<='],
            collections: [],
            selectedCollection: '',
            searchHint: '',
            fields: {},
            links: {},
            rows: [],
            meta: { page: 1, pages: 1, total: 0, per_page: 25, sort: 'id', dir: 'ASC' },
            summaryFields: [],
            q: '',
            filters: [],
            showAdvanced: false,
            loading: false,
            errorMessage: '',
            showLanding: true,
            generatedAt: null,
            permalinkUrl: window.location.href,
            _filterChangeTimeout: null,
            basePath: (window.IPZ_ROUTE && window.IPZ_ROUTE.basePath) ? window.IPZ_ROUTE.basePath : '',

            get currentCollection() {
                return this.collections.find(function (item) {
                    return item.key === this.selectedCollection;
                }.bind(this)) || null;
            },

            get fieldNames() {
                return Object.keys(this.fields || {});
            },

            resolveFieldKey: function (input) {
                var candidate = String(input || '').trim();
                if (!candidate) {
                    return '';
                }

                if (this.fields[candidate]) {
                    return candidate;
                }

                var candidateLower = candidate.toLowerCase();
                var keys = this.fieldNames;
                var i;

                for (i = 0; i < keys.length; i++) {
                    if (String(keys[i]).toLowerCase() === candidateLower) {
                        return keys[i];
                    }
                }

                for (i = 0; i < keys.length; i++) {
                    var label = this.fieldLabel(keys[i]);
                    if (String(label || '').trim().toLowerCase() === candidateLower) {
                        return keys[i];
                    }
                }

                return '';
            },

            normalizeLoadedFilters: function (filters) {
                var defaultField = this.fieldNames[0] || '';
                return (Array.isArray(filters) ? filters : []).map(function (filter) {
                    var resolvedField = this.resolveFieldKey(filter && filter.field);
                    var operator = filter && filter.operator ? String(filter.operator) : 'like';
                    var value = filter && filter.value !== undefined && filter.value !== null ? String(filter.value) : '';

                    if (this.operators.indexOf(operator) === -1) {
                        operator = 'like';
                    }

                    return {
                        field: resolvedField || defaultField,
                        operator: operator,
                        value: value
                    };
                }.bind(this));
            },

            get resultTitle() {
                if (this.q || this.filters.length > 0) {
                    return 'Rezultati iskanja';
                }
                return 'Seznam zapisov';
            },

            get summaryText() {
                return 'Skupaj: ' + this.meta.total + ' | Stran: ' + this.meta.page + '/' + this.meta.pages;
            },

            get pageInfo() {
                return this.meta.page + ' / ' + this.meta.pages;
            },

            get formattedGeneratedAt() {
                if (!this.generatedAt) {
                    return '';
                }

                const date = this.generatedAt.toLocaleDateString('sl-SI', {
                    day: 'numeric',
                    month: 'numeric',
                    year: 'numeric'
                });

                const time = this.generatedAt.toLocaleTimeString('sl-SI', {
                    hour: 'numeric',
                    minute: '2-digit'
                });

                return `${date} ob ${time}`;
            },

            get baseUrl() {
                try {
                    var url = new URL(this.permalinkUrl);
                    return url.origin + url.pathname;
                } catch (e) {
                    return this.permalinkUrl || '';
                }
            },

            init: async function () {
                try {
                    await this.loadCollections();
                    this.applyInitialQuery();
                    if (!this.showLanding) {
                        await this.search();
                    }
                } catch (error) {
                    this.errorMessage = error.message || 'Inicializacija ni uspela.';
                }
            },

            loadCollections: async function () {
                var response = await fetch(this.apiUrl('collections.php'));
                var data = await response.json();
                if (!data.ok) {
                    throw new Error('Neuspesno branje zbirk.');
                }
                this.collections = data.collections || [];
            },

            parseFiltersParam: function (value) {
                if (!value) {
                    return [];
                }
                try {
                    var decoded = JSON.parse(value);
                    return Array.isArray(decoded) ? decoded : [];
                } catch (e) {
                    return [];
                }
            },

            resolvePrettyCollection: function () {
                var parts = window.location.pathname.split('/').filter(function (part) {
                    return part !== '';
                });

                if (parts.length < 2) {
                    return {};
                }

                var collection = parts[parts.length - 1];
                if (collection === 'index.php' || /^[0-9]+$/.test(collection)) {
                    return {};
                }

                return {
                    collection: decodeURIComponent(collection),
                    basePath: parts.length > 1 ? '/' + parts.slice(0, -1).join('/') : ''
                };
            },

            applyInitialQuery: function () {
                var params = new URLSearchParams(window.location.search);
                var route = window.IPZ_ROUTE || {};
                var prettyRoute = this.resolvePrettyCollection();
                var collection = params.get('collection') || route.collection || prettyRoute.collection;
                var legacyBaza = params.get('baza');
                var q = params.get('q') || params.get('query') || '';
                if (prettyRoute.basePath) {
                    this.basePath = prettyRoute.basePath;
                }

                if (!collection && legacyBaza) {
                    collection = legacyBaza;
                }

                if (collection) {
                    var existing = this.collections.find(function (item) {
                        return item.key === collection || (item.aliases || []).indexOf(collection) !== -1;
                    });
                    if (existing) {
                        this.selectedCollection = existing.key;
                        this.showLanding = false;
                    }
                }

                this.q = q;
                this.meta.page = Math.max(1, parseInt(params.get('page') || '1', 10));
                this.meta.per_page = Math.max(1, parseInt(params.get('per_page') || '25', 10));
                this.meta.sort = params.get('sort') || 'id';
                this.meta.dir = (params.get('dir') || 'ASC').toUpperCase() === 'DESC' ? 'DESC' : 'ASC';
                this.filters = this.parseFiltersParam(params.get('filters'));

                if (!this.showLanding) {
                    this.syncCollectionConfig();
                    this.filters = this.normalizeLoadedFilters(this.filters);
                    this.generatedAt = new Date();
                }
            },

            syncCollectionConfig: function () {
                var selected = this.currentCollection;
                if (!selected) {
                    this.fields = {};
                    this.links = {};
                    this.summaryFields = [];
                    this.searchHint = '';
                    return;
                }
                this.fields = selected.fields || {};
                this.links = selected.links || {};
                this.summaryFields = selected.summary || Object.keys(this.fields || {}).slice(0, 3);
                this.searchHint = selected.search_hint || 'Vnesi iskalni niz';
            },

            onCollectionChange: function () {
                this.showLanding = false;
                this.syncCollectionConfig();
                this.filters = [];
                this.meta.page = 1;
                this.search();
            },

            addFilter: function () {
                if (this.fieldNames.length === 0) {
                    return;
                }
                this.filters.push({ field: this.fieldNames[0], operator: 'like', value: '' });
            },

            removeFilter: function (idx) {
                this.filters.splice(idx, 1);
                this.meta.page = 1;
                this.search();
            },

            onFilterChange: function () {
                this.meta.page = 1;
                this.search();
            },

            debouncedFilterChange: function () {
                var self = this;
                if (this._filterChangeTimeout) {
                    clearTimeout(this._filterChangeTimeout);
                }
                this._filterChangeTimeout = setTimeout(function () {
                    self.meta.page = 1;
                    self.search();
                }, 800);
            },

            normalizeFilters: function () {
                return this.filters.filter(function (item) {
                    return item.field && item.operator && String(item.value || '').trim() !== '';
                }).map(function (item) {
                    return { field: item.field, operator: item.operator, value: String(item.value).trim() };
                });
            },

            apiUrl: function (file) {
                return (this.basePath || '') + '/api/' + file;
            },

            collectionUrl: function (collection) {
                return (this.basePath || '') + '/' + encodeURIComponent(collection);
            },

            buildResultsUrl: function (includeShareParams) {
                if (!this.selectedCollection) {
                    return (this.basePath || '') + '/';
                }

                var url = this.collectionUrl(this.selectedCollection);
                if (!includeShareParams) {
                    return url + '#results';
                }

                var params = new URLSearchParams();
                if (this.q) {
                    params.set('q', this.q);
                }
                params.set('page', String(this.meta.page));
                params.set('per_page', String(this.meta.per_page));
                if (this.meta.sort) {
                    params.set('sort', this.meta.sort);
                }
                if (this.meta.dir) {
                    params.set('dir', this.meta.dir);
                }

                var activeFilters = this.normalizeFilters();
                if (activeFilters.length > 0) {
                    params.set('filters', JSON.stringify(activeFilters));
                }

                var query = params.toString();
                return url + (query ? '?' + query : '') + '#results';
            },

            syncUrl: function () {
                if (this.showLanding || !this.selectedCollection) {
                    history.replaceState(null, '', (this.basePath || '') + '/');
                    this.permalinkUrl = window.location.href;
                    return;
                }

                var activeFilters = this.normalizeFilters();
                if (activeFilters.length > 0) {
                    this.showAdvanced = true;
                }

                history.replaceState(null, '', this.buildResultsUrl(false));
                this.permalinkUrl = window.location.href;
            },

            copyShareUrl: async function () {
                var shareUrl = new URL(this.buildResultsUrl(true), window.location.origin).href;
                try {
                    await navigator.clipboard.writeText(shareUrl);
                    alert('URL je bil kopiran v odložišče.');
                } catch (e) {
                    alert('Kopiranje ni uspelo. Uporabite naslednji URL:\n' + shareUrl);
                }
            },

            search: async function () {
                this.loading = true;
                this.errorMessage = '';
                try {
                    var payload = {
                        collection: this.selectedCollection,
                        q: this.q,
                        page: this.meta.page,
                        per_page: this.meta.per_page,
                        sort: this.meta.sort,
                        dir: this.meta.dir,
                        filters: this.normalizeFilters()
                    };

                    var response = await fetch(this.apiUrl('search.php'), {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });

                    var data = await response.json();
                    if (!data.ok) {
                        throw new Error(data.error || 'Napaka pri iskanju.');
                    }

                    this.rows = data.rows || [];
                    this.meta = data.meta || this.meta;
                    this.summaryFields = (data.meta && data.meta.summary) ? data.meta.summary : this.summaryFields;
                    this.fields = (data.meta && data.meta.fields) ? data.meta.fields : this.fields;
                    this.links = (data.meta && data.meta.links) ? data.meta.links : this.links;
                    this.showLanding = false;
                    this.generatedAt = new Date();
                    this.syncUrl();
                } catch (error) {
                    this.errorMessage = error.message || 'Napaka pri branju rezultatov.';
                } finally {
                    this.loading = false;
                }
            },

            changePage: function (delta) {
                var next = this.meta.page + delta;
                if (next < 1 || next > this.meta.pages) {
                    return;
                }
                this.meta.page = next;
                this.search();
            },

            handleResultClick: function (event) {
                var target = event.target.closest('a.chip[data-filter-field]');
                if (!target) {
                    return;
                }
                event.preventDefault();
                var field = target.getAttribute('data-filter-field');
                var value = target.getAttribute('data-filter-value');
                this.applyFilterChip(field, value);
            },

            applyFilterChip: function (field, value) {
                var resolvedField = this.resolveFieldKey(field) || this.fieldNames[0] || '';
                if (!resolvedField) {
                    return;
                }
                this.showAdvanced = true;
                this.filters.push({ field: resolvedField, operator: 'like', value: String(value || '').trim() });
                this.meta.page = 1;
                this.search();
            },

            detailsUrl: function (id) {
                return this.collectionUrl(this.selectedCollection) + '/' + encodeURIComponent(String(id));
            },

            fieldLabel: function (field) {
                if (!this.fields[field]) {
                    return field;
                }
                return this.fields[field].label || field;
            },

            operatorLabel: function (op) {
                const labels = {
                    'like': 'vsebuje',
                    'not like': 'ne vsebuje',
                    '=': 'je enako',
                    '<>': 'ni enako',
                    '>': 'večje kot',
                    '<': 'manjše kot',
                    '>=': 'večje ali enako',
                    '<=': 'manjše ali enako'
                };
                return labels[op] || op;
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

            renderCell: function (row, field) {
                var value = row[field];

                // Special handling for slovlit grouping
                if (this.selectedCollection === 'slovlit') {
                    if (field === 'priimek') {
                        var fullName = (row.ime ? row.ime + ' ' : '') + (row.priimek || '');
                        if (row.LinkAvtor) {
                            return '<a class="external-link" target="_blank" rel="noopener" href="' + this.escapeHtml(row.LinkAvtor) + '">' + this.escapeHtml(fullName) + '</a>';
                        }
                        return this.escapeHtml(fullName);
                    }
                    if (field === 'naslov' && row.LinkDelo) {
                        return '<a class="external-link" target="_blank" rel="noopener" href="' + this.escapeHtml(row.LinkDelo) + '">' + this.escapeHtml(value) + '</a>';
                    }
                }

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
                    return '<a href="#" class="chip" data-filter-field="' + this.escapeHtml(field) + '" data-filter-value="' + safeValue + '">' + safeValue + '</a>';
                }

                if (type === 'list') {
                    return this.splitListParts(value).map(function (item) {
                        var safeItem = this.escapeHtml(item.text);
                        var chip = '<a href="#" class="chip" data-filter-field="' + this.escapeHtml(field) + '" data-filter-value="' + safeItem + '">' + safeItem + '</a>';
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
