<?php
/**
 * @var \Tk\Plantuml\Models\Problem $problem
 */
$pageTitle = "#" . $problem->getID() . " " . $problem->getTitle();
$needsEditor = true;
require __DIR__.'/partials/header.php';
?>

<div class="container-fluid py-4 px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <span class="text-muted">#<?php echo $problem->getID(); ?></span>
            <?php echo $problem->getTitle(); ?>
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
                            <button class="btn btn-sm btn-outline-primary" id="png-download" disabled>
                                <i class="bi bi-download"></i> PNG
                            </button>
                            <button class="btn btn-sm btn-outline-primary" id="svg-download" disabled>
                                <i class="bi bi-download"></i> SVG
                            </button>
                            <button class="btn btn-sm btn-outline-primary" id="txt-download" disabled>
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
                        <div class="btn-tab-group">
                            <button class="btn btn-sm btn-outline-primary">UML diagram</button>
                            <button class="btn btn-sm btn-outline-primary">UML code</button>
                        </div>
                    </div>
                </div>
                <div class="card-body overflow-auto" style="height: 600px;">
                    <div id="solutionDiagram" style="display: none;"></div>
                    <div id="solutionCode" style="display: none;">
                        <pre class="mb-0"><code><?php echo $problem->getUML(); ?></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs/loader.js"></script>
<script>
    const umlCode = <?php echo json_encode($problem->getUML()); ?>;
</script>
<script src="/js/editor.js"></script>
</body>
</html>

