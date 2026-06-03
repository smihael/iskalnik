<?php
function ipz_public_base_path($routeSegments)
{
    $requestPath = isset($_SERVER['REQUEST_URI'])
        ? (string) parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
        : '';

    if ($routeSegments > 0 && $requestPath !== '') {
        $parts = explode('/', trim($requestPath, '/'));
        if (count($parts) >= $routeSegments) {
            $baseParts = array_slice($parts, 0, count($parts) - $routeSegments);
            return empty($baseParts) ? '' : '/' . implode('/', $baseParts);
        }
    }

    return rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
}

$basePath = ipz_public_base_path(isset($_GET['collection']) && $_GET['collection'] !== '' ? 1 : 0);
if ($basePath === '/') {
    $basePath = '';
}
$route = [
    'collection' => isset($_GET['collection']) ? (string) $_GET['collection'] : '',
    'basePath' => $basePath,
];
?><!doctype html>
<html lang="sl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iskalniki po zbirkah</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath . '/public/styles.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body x-data="searchApp()" x-init="init()" x-effect="document.title = (currentCollection ? currentCollection.title + ' - Iskalnik' : 'Iskalniki po zbirkah')">
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="<?php echo htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8'); ?>">Iskalnik po podatkovni zbirki</a>
            <nav x-data="{ open: false }">
                <div class="dropdown" @click="open = !open" @click.away="open = false">
                    <span>Povezave ▾</span>
                    <div class="dropdown-menu" x-show="open" x-transition>
                        <div class="dropdown-header">Zbirke</div>
                        <template x-for="item in collections" :key="item.key">
                            <a class="dropdown-item" :href="collectionUrl(item.key)" x-text="item.title"></a>
                        </template>
                        <div class="dropdown-header">Orodja</div>
                        <a class="dropdown-item" href="/admin/">Urejevalnik baze</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <main class="layout">
        <section class="hero card">
            <h1 x-text="currentCollection && !showLanding ? currentCollection.title : 'Iskanje po zbirkah'"></h1>
            <div class="lead" x-show="!showLanding && currentCollection" x-html="currentCollection.about"></div>
            <p class="lead" x-show="showLanding">Enotna vstopna točka za iskanje po digitalnih humanističnih zbirkah.</p>
        </section>

        <section class="card" x-show="showLanding">
            <h2>Izberi zbirko</h2>
            <div class="grid">
                <template x-for="item in collections" :key="item.key">
                    <article class="card card-collection">
                        <h3 x-text="item.title"></h3>
                        <p x-text="item.intro || (item.n_entries ? 'Število zapisov: ' + item.n_entries : 'Iskalnik po podatkovni zbirki.')"></p>
                        <a class="btn-link" :href="collectionUrl(item.key)">Odpri zbirko</a>
                    </article>
                </template>
            </div>
        </section>

        <section class="controls card" x-show="!showLanding">
            <div class="row">
                <input id="quick-query" type="text" :placeholder="searchHint || 'Vnesi iskalni niz'" x-model="q" @keydown.enter.prevent="search()">
                <button id="search-btn" type="button" @click="search()" :disabled="loading" title="Išči">
                    <svg x-show="!loading" viewBox="0 0 24 24" width="18" height="18" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                    </svg>
                    <span x-show="loading">...</span>
                </button>
            </div>

            <div class="row-advanced">
                <button
                    type="button"
                    class="advanced-toggle"
                    :class="{ 'is-active': showAdvanced }"
                    :aria-expanded="showAdvanced.toString()"
                    aria-controls="advanced-filters"
                    @click="showAdvanced = !showAdvanced"
                >
                    <span class="toggle-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </span>
                    <span>Podrobno iskanje</span>
                    <span class="toggle-switch" aria-hidden="true"></span>
                </button>
            </div>

            <div id="advanced-filters" class="advanced-filters" x-show="showAdvanced" x-transition>
                <div class="advanced-header">
                    <span>Dodatni filtri</span>
                </div>
                <template x-for="(filter, idx) in filters" :key="idx">
                    <div class="filter-row">
                        <select x-model="filter.field" @change="onFilterChange()">
                            <template x-for="fieldName in fieldNames" :key="fieldName">
                                <option :value="fieldName" :selected="filter.field === fieldName" x-text="fieldLabel(fieldName)"></option>
                            </template>
                        </select>
                        <select x-model="filter.operator" @change="onFilterChange()">
                            <template x-for="op in operators" :key="op">
                                <option :value="op" :selected="filter.operator === op" x-text="operatorLabel(op)"></option>
                            </template>
                        </select>
                        <input type="text" x-model="filter.value" @keydown.enter.prevent="search()" @input="debouncedFilterChange()" placeholder="Vrednost filtra">
                        <button type="button" class="secondary" @click="removeFilter(idx)">Odstrani</button>
                    </div>
                </template>
                <div class="advanced-actions">
                    <button type="button" class="secondary btn-action" @click="addFilter()">
                        <span aria-hidden="true">+</span>
                        <span>Dodaj pogoj</span>
                    </button>
                    <div class="advanced-actions-right">
                        <button type="button" class="secondary btn-action" @click="search()" :disabled="loading" title="Uporabi filtre">
                            <svg x-show="!loading" viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon>
                            </svg>
                            <span x-show="!loading">Filtriraj</span>
                            <span x-show="loading">...</span>
                        </button>
                        <button type="button" class="secondary btn-action btn-share" @click="copyShareUrl()" :disabled="loading" title="Deli filter">
                            <svg viewBox="0 0 24 24" width="16" height="16" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                                <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                            </svg>
                            <span>Deli filter</span>
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <section class="results card" x-show="!showLanding" id="results">
            <div class="result-head">
                <h2 id="result-title" x-text="resultTitle"></h2>
                <div id="summary" class="summary" x-text="summaryText"></div>
            </div>

            <div id="result-table-wrap" class="table-wrap" @click="handleResultClick($event)">
                <table x-show="rows.length > 0">
                    <thead>
                        <tr>
                            <template x-for="field in summaryFields" :key="field">
                                <th x-text="fieldLabel(field)"></th>
                            </template>
                            <th class="col-info">Orodja</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in rows" :key="row.id || JSON.stringify(row)">
                            <tr>
                                <template x-for="field in summaryFields" :key="field">
                                    <td>
                                        <div x-html="renderCell(row, field)"></div>
                                    </td>
                                </template>
                                <td>
                                    <a class="detail-icon" :href="detailsUrl(row.id)" :aria-label="'Podrobnosti zapisa ' + row.id" title="Podrobnosti">
                                        <svg xmlns="http://www.w3.org/1999/xlink" viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon-info"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
                <p x-show="!loading && rows.length === 0">Ni zadetkov za izbrane kriterije.</p>
                <p x-show="loading">Nalagam rezultate ...</p>
                <p x-show="errorMessage" x-text="errorMessage"></p>
            </div>

            <div class="pagination">
                <button id="prev-page" type="button" class="secondary" @click="changePage(-1)" :disabled="meta.page <= 1 || loading">Prejšnja</button>
                <span id="page-info" x-text="pageInfo"></span>
                <button id="next-page" type="button" class="secondary" @click="changePage(1)" :disabled="meta.page >= meta.pages || loading">Naslednja</button>
            </div>
        </section>

        <footer class="site-footer">
            <div class="layout footer-inner">
                <template x-if="currentCollection && !showLanding">
                    <div class="colophon-block">
                        <small>
                            Prikaz je bil ustvarjen <span x-text="formattedGeneratedAt"></span> prek <a :href="permalinkUrl" target="_blank" rel="noopener" x-text="baseUrl"></a><br />
                            <span x-show="currentCollection.credits" x-html="currentCollection.credits"></span>
                        </small>
                    </div>
                </template>
                <p x-show="!currentCollection || showLanding"></p>
            </div>
        </footer>
    </main>

    <script>
        window.IPZ_ROUTE = <?php echo json_encode($route, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="<?php echo htmlspecialchars($basePath . '/public/app-alpine.js', ENT_QUOTES, 'UTF-8'); ?>" defer></script>
    <script defer src="<?php echo htmlspecialchars($basePath . '/public/alpine.min.js', ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
