class UMLEditor
{
    constructor(containerId, umlCode) {
        this.containerId = containerId;
        this.umlCode = umlCode;
        this.editor = null;
        this.debounceTimeout = null;
        this.buttons = this.initializeButtons();
    }

    initialize() {
        require.config({
            paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs' }
        });

        require(['vs/editor/editor.main'], () => {
            this.createEditor();
            this.setupEventListeners();
            this.showTab('solutionDiagram');
            this.updateUMLDiagram(this.umlCode, 'solutionDiagram');
        });
    }

    initializeButtons() {
        const buttons = {};
        ['png', 'svg', 'txt'].forEach(format => {
            const button = document.getElementById(`${format}-download`);
            if (button) {
                buttons[format] = button;
                button.addEventListener('click', () => this.downloadUML(format));
            }
        });
        return buttons;
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

    setupEventListeners() {
        // Editor content change event
        this.editor.onDidChangeModelContent(() => {
            const currentUML = this.editor.getValue();
            this.debounce(() => this.updateUMLDiagram(currentUML, 'preview'), 1000);
        });

        // Tab change event
        document.querySelectorAll('.btn-tab-group .btn').forEach(button => {
            button.addEventListener('click', (e) => {
                const tabName = e.target.textContent.includes('UML diagram')
                    ? 'solutionDiagram'
                    : 'solutionCode';
                this.showTab(tabName);
            });
        });
    }

    updateUMLDiagram(umlCode, targetId) {
        const element = document.getElementById(targetId);
        const isPreview = targetId === 'preview';

        const trimmedUML = umlCode.trim();
        if (!trimmedUML || trimmedUML === '// write from here') {
            element.innerHTML = '';
            if (isPreview) {
                this.setButtonsState(false);
            }
            return;
        }

        element.innerHTML = this.getLoadingSpinner();
        element.style.display = 'block';

        if (isPreview) {
            this.setButtonsState(false);
        }

        fetch('/api/uml/preview', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `uml=${encodeURIComponent(umlCode)}&format=svg`
        })
            .then(async response => {
                const text = await response.text();
                if (!response.ok) {
                    throw new Error(text);
                }
                return text;
            })
            .then(svgContent => {
                element.innerHTML = svgContent;
                if (isPreview) {
                    this.setButtonsState(true);
                }
            })
            .catch(error => {
                element.innerHTML = this.getErrorMessage(error.message);
            });
    }

    setButtonsState(enabled) {
        Object.values(this.buttons).forEach(button => {
            button.disabled = !enabled;
        });
    }

    debounce(func, wait) {
        clearTimeout(this.debounceTimeout);
        this.debounceTimeout = setTimeout(func, wait);
    }

    downloadUML(format) {
        const content = this.editor.getValue();

        if (format === 'txt') {
            const blob = new Blob([content], { type: 'text/plain' });
            this.downloadFile(blob, `uml-diagram.txt`);
            return;
        }

        fetch('/api/uml/export', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `uml=${encodeURIComponent(content)}&format=${format}`
        })
            .then(response => {
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text);
                    });
                }
                return response.blob();
            })
            .then(blob => this.downloadFile(blob, `uml-diagram.${format}`))
            .catch(error => {
                alert('Failed to download UML diagram: ' + error.message);
            });
    }

    downloadFile(blob, filename) {
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    }

    showTab(tabName) {
        const tabs = ['solutionDiagram', 'solutionCode'];
        tabs.forEach(tab => {
            const element = document.getElementById(tab);
            element.style.display = tab === tabName ? 'block' : 'none';
        });

        document.querySelectorAll('.btn-tab-group .btn').forEach(btn => {
            if (btn.textContent.includes('UML diagram') && tabName === 'solutionDiagram' ||
                btn.textContent.includes('UML code') && tabName === 'solutionCode') {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
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

    getErrorMessage(message) {
        return `
            <div class="alert alert-danger" role="alert">
                ${message}
            </div>
        `;
    }
}

const editor = new UMLEditor('editor-container', umlCode);
editor.initialize();