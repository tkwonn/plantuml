<?php
/**
 * @var Models\Problem $problem
 */
$pageTitle = '#' . htmlspecialchars((string) $problem->getID(), ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($problem->getTitle(), ENT_QUOTES, 'UTF-8');
$needsEditor = true;
require __DIR__ . '/layout/header.php';
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <span class="text-muted">#<?php echo htmlspecialchars((string) $problem->getID(), ENT_QUOTES, 'UTF-8'); ?></span>
            <?php echo htmlspecialchars($problem->getTitle(), ENT_QUOTES, 'UTF-8'); ?>
        </h2>
        <a href="/" class="btn btn-outline-primary">
            <i class="bi bi-caret-left-fill"></i> Problem List
        </a>
    </div>

    <div class="row g-4">
        <!-- Editor Pane -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Editor</h5>
                </div>
                <div class="card-body p-0" style="height: 600px;">
                    <div id="editor-container" class="h-100"></div>
                </div>
            </div>
        </div>

        <!-- Preview Pane -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Preview</h5>
                        <div class="btn-download-group">
                            <button class="btn btn-sm btn-outline-primary download-btn" 
                                    onclick="editor.exportDiagram('png')"
                                    disabled>
                                <i class="bi bi-download"></i> PNG
                            </button>
                            <button class="btn btn-sm btn-outline-primary download-btn" 
                                    onclick="editor.exportDiagram('svg')"
                                    disabled>
                                <i class="bi bi-download"></i> SVG
                            </button>
                            <button class="btn btn-sm btn-outline-primary download-btn" 
                                    onclick="editor.exportDiagram('txt')"
                                    disabled>
                                <i class="bi bi-download"></i> TXT
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="height: 600px;">
                    <div id="preview"></div>
                </div>
            </div>
        </div>

        <!-- Solutions Pane -->
        <div class="col-md-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Solutions</h5>
                        <div class="btn-tab-group" role="tablist">
                            <button class="btn btn-sm btn-outline-primary"
                                    role="tab"
                                    onclick="editor.showTab('solutionDiagram')"
                                    aria-controls="solutionDiagram"
                                    aria-selected="true">
                                UML diagram
                            </button>
                            <button class="btn btn-sm btn-outline-primary"
                                    role="tab"
                                    onclick="editor.showTab('solutionCode')"
                                    aria-controls="solutionCode"
                                    aria-selected="false">
                                UML code
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="height: 600px;">
                    <div id="solutionDiagram" role="tabpanel" style="display: none;"></div>
                    <div id="solutionCode" role="tabpanel" style="display: none;">
                        <pre class="mb-0"><code><?php echo htmlspecialchars($problem->getUML() ?? '', ENT_QUOTES, 'UTF-8'); ?></code></pre>
                    </div>
                </div>
            </div>
        </div>

        <!-- Export Form -->
        <form id="export-form" method="POST" action="/api/uml/export?id=<?php echo urlencode((string) $problem->getID()); ?>" style="display:none;">
            <input type="hidden" name="uml" value="">
            <input type="hidden" name="format" value="">
        </form>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    class Editor
    {
        constructor(containerId, umlCode) {
            this.containerId = containerId;
            this.editor = null;
            this.debounceTimeout = null;
            this.umlCode = umlCode;
        }

        initialize() {
            require.config({
                paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs' }
            });

            require(['vs/editor/editor.main'], () => {
                this.createEditor();
                this.setChangeEvent();
                this.showTab('solutionDiagram');
                this.updateDiagram(this.umlCode, 'solutionDiagram');
            });
        }

        createEditor() {
            this.editor = monaco.editor.create(document.getElementById(this.containerId), {
                value: '// write from here',
                language: 'plaintext',
                theme: 'vs',
                minimap: { enabled: false },
                automaticLayout: true
            });
        }

        setChangeEvent() {
            // Editor content change event
            this.editor.onDidChangeModelContent(() => {
                const currentUML = this.editor.getValue();
                this.debounce(() => this.updateDiagram(currentUML, 'preview'), 1000);
            });
        }

        showTab(tabName) {
            // Toggle panel visibility
            document.querySelectorAll('[role="tabpanel"]').forEach(panel => {
                panel.style.display = panel.id === tabName ? 'block' : 'none';
            });

            // Toggle tab button styles
            document.querySelectorAll('[role="tab"]').forEach(tab => {
                const isSelected = tab.getAttribute('aria-controls') === tabName;
                tab.setAttribute('aria-selected', isSelected);
                tab.classList.toggle('active', isSelected);
            });
        }

        updateDiagram(umlCode, targetId) {
            const element = document.getElementById(targetId);
            const isPreview = (targetId === 'preview');

            const trimmedUML = umlCode.trim();
            if (!trimmedUML || trimmedUML === '// write from here') {
                element.innerHTML = '';
                if (isPreview) {
                    this.toggleDownloadButtons(false);
                }
                return;
            }

            element.innerHTML = this.getLoadingSpinner();
            element.style.display = 'block';

            if (isPreview) {
                this.toggleDownloadButtons(false);
            }

            fetch('/api/uml/preview', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `uml=${encodeURIComponent(umlCode)}&format=svg`
            })
                .then(async response => {
                    const responseText = await response.text();
                    if (!response.ok) {
                        throw new Error(responseText);
                    }
                    return responseText;
                })
                .then(responseText => {
                    element.innerHTML = responseText;
                    if (isPreview) {
                        this.toggleDownloadButtons(true);
                    }
                })
                .catch(error => {
                    element.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            ${error.message}
                        </div>
                    `;
                });
        }

        exportDiagram(format) {
            const content = this.editor.getValue();

            const form = document.getElementById('export-form');
            const umlInput = form.querySelector('input[name="uml"]');
            const formatInput = form.querySelector('input[name="format"]');

            umlInput.value = content;
            formatInput.value = format;
            form.submit();
        }

        debounce(func, wait) {
            clearTimeout(this.debounceTimeout);
            this.debounceTimeout = setTimeout(func, wait);
        }

        toggleDownloadButtons(enabled) {
            document.querySelectorAll('.download-btn').forEach(button => {
                button.disabled = !enabled;
            });
        }

        getLoadingSpinner() {
            return `
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px">
                    <div class="spinner-border text-secondary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
        }
    }
    const umlCode = <?php echo json_encode($problem->getUML()); ?>; 
    const editor = new Editor('editor-container', umlCode);
    editor.initialize();
</script>
</body>
</html>

