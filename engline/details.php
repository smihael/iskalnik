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

$basePath = ipz_public_base_path(isset($_GET['collection'], $_GET['id']) && $_GET['collection'] !== '' && $_GET['id'] !== '' ? 2 : 0);
if ($basePath === '/') {
    $basePath = '';
}
$route = [
    'collection' => isset($_GET['collection']) ? (string) $_GET['collection'] : '',
    'id' => isset($_GET['id']) ? (string) $_GET['id'] : '',
    'basePath' => $basePath,
];
?><!doctype html>
<html lang="sl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Podrobnosti</title>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($basePath . '/public/styles.css', ENT_QUOTES, 'UTF-8'); ?>">
</head>
<body x-data="detailsApp()" x-init="init()" x-effect="document.title = title">
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="<?php echo htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8'); ?>">Iskalnik po podatkovna zbirki</a>
            <nav>
                <a href="<?php echo htmlspecialchars($basePath . '/', ENT_QUOTES, 'UTF-8'); ?>">Nazaj na iskanje</a>
            </nav>
        </div>
    </header>

    <main class="layout" id="details">
        <section class="hero card">
            <h3 class="lead" x-text="title"></h3>
        </section>

        <section class="card">
            <p x-show="loading">Nalagam podrobnosti ...</p>
            <p x-show="errorMessage" x-text="errorMessage"></p>

            <table x-show="!loading && row">
                <tbody>
                    <template x-for="field in fieldNames" :key="field">
                        <tr x-show="row[field] !== null && row[field] !== ''">
                            <td x-text="fieldLabel(field)"></td>
                            <td><div x-html="renderCell(field, row[field])"></div></td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <p><a class="detail-link" :href="backUrl">Nazaj na rezultate</a></p>
        </section>
    </main>

    <footer class="site-footer">
        <div class="layout footer-inner">
            <div class="colophon-block">
                <small>
                    Prikaz je bil ustvarjen <span x-text="formattedGeneratedAt"></span> prek <a :href="permalink" target="_blank" rel="noopener" x-text="permalink"></a><br />
                    <span x-show="credits" x-html="credits"></span>
                </small>
            </div>
        </div>
    </footer>

    <script>
        window.IPZ_ROUTE = <?php echo json_encode($route, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES); ?>;
    </script>
    <script src="<?php echo htmlspecialchars($basePath . '/public/details.js', ENT_QUOTES, 'UTF-8'); ?>" defer></script>
    <script defer src="<?php echo htmlspecialchars($basePath . '/public/alpine.min.js', ENT_QUOTES, 'UTF-8'); ?>"></script>
</body>
</html>
