
let Watson = {
    Templates: {
        Agent: document.createElement('template'),
        Icon: document.createElement('template')
    }
};

Watson.Templates.Agent.innerHTML = `
    <style>
        :host {
            display: flex;
            display: none;
        }
        :host([opened]) {
            display: flex;
        }
    
        .watson-dialog {
            display: flex;
            align-items: center;
            justify-content: center;
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: var(--watson-dialog-z-index);
        }
    
        .watson-dialog__panel {
            display: flex;
            flex-direction: column;
            z-index: 2;
            width: var(--watson-dialog-width);
            max-width: calc(100% - 2rem);
            max-height: calc(100% - 2rem);
            background-color: var(--watson-dialog-panel-background-color);
            border-radius: var(--watson-dialog-panel-border-radius);
        }
    
        .watson-dialog__panel:focus {
            outline: none;
        }

        /* Ensure there's enough vertical padding for phones that don't update vh when chrome appears (e.g. iPhone) */
        @media screen and (max-width: 420px) {
            .watson-dialog__panel {
                max-height: 80vh;
            }
        }
        
        .watson-dialog__header {
            display: flex;
            flex: 0 0 auto;
            align-items: center;
            padding: var(--watson-dialog-spacing-y) var(--watson-dialog-spacing-x);
        }
        
        .watson-dialog__input {
            flex: 1 1 auto;
            font: inherit;
            margin: 0;
            padding: 0 .5rem;
            background-color: var(--watson-dialog-input-background-color);
        }
        .watson-dialog__input__control {
            display: block;
            width: 100%;
            outline: none;
            background-color: transparent;
            border: 0;
            box-shadow: none;
            font-size: 200%;
            line-height: 1.5;
        }
        
        .watson-dialog__logo {
            display: flex;
            flex-shrink: 0;
            flex-wrap: wrap;
            justify-content: end;
            padding: 0 0 0 1rem;
            font-size: 300%;
        }

        .watson-dialog__body {
            flex: 1 1 auto;
            display: block;
            padding: var(--watson-dialog-spacing-y) var(--watson-dialog-spacing-x);
            overflow: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .watson-dialog__footer {
            flex: 0 0 auto;
            padding: var(--watson-dialog-spacing-y) var(--watson-dialog-spacing-x);
            text-align: right;
        }
        
        .watson-dialog__overlay {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background-color: var(--watson-dialog-overlay-background-color);
            opacity: var(--watson-dialog-overlay-opacity);
        }
        
        .watson-list-group {
            display: flex;
            flex-direction: column;
            max-height: 35rem;
            padding: 0;
            margin: 0;
            overflow: auto;
        }
        .watson-list-group-header {
            padding: var(--watson-list-group-header-padding-y) var(--watson-list-group-header-padding-x);
            background-color: var(--watson-list-group-header-background-color);
            border-bottom: var(--watson-list-group-header-border-width) solid var(--watson-list-group-header-border-color);
            color: var(--watson-list-group-header-color);
            font-size: 75%;
            font-weight: 600;
            line-height: 1;
            text-transform: uppercase;
        }
        .watson-list-group-item {
            position: relative;
            display: block;
            padding: var(--watson-list-group-item-padding-y) var(--watson-list-group-item-padding-x);
            background-color: var(--watson-list-group-item-background-color);
            border-bottom: var(--watson-list-group-item-border-width) solid var(--watson-list-group-item-border-color);
            color: var(--waton-list-group-item-color);
        }
        .watson-list-group-item.selected {
            background-color: var(--watson-selected-background-color);        
        }
        
        .watson-row {
            --watson-gutter-x: var(--watson-spacing-4);
            --watson-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(-1 * var(--watson-gutter-y));
            margin-right: calc(-.5 * var(--watson-gutter-x));
            margin-left: calc(-.5 * var(--watson-gutter-x));
        }
        
        .watson-row > * {
            min-width: 0;
            max-width: 100%;
            margin-top: var(--watson-gutter-y);
            padding-right: calc(var(--watson-gutter-x) * .5);
            padding-left: calc(var(--watson-gutter-x) * .5);
        }
        .watson-col {
            flex: 1 0 0%;
        }
        .watson-col-auto {
            flex: 0 0 auto;
            width: auto;
        }
        .watson-sticky-top {
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        .watson-text-small {
            color: var(--watson-color-600);
            font-size: 85%;
        }
        .watson-text-truncate {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>
    
    <div part="base" class="watson-dialog">
        <div part="overlay" class="watson-dialog__overlay" tabindex="-1"></div>
        <div part="panel" class="watson-dialog__panel" role="dialog" aria-modal="true">
            <header part="header" class="watson-dialog__header">
                <div class="watson-dialog__input">
                    <input class="watson-dialog__input__control" id="text-input" />
                </div>
                <div class="watson-dialog__logo">
                    <watson-icon></watson-icon>
                </div>
            </header>
            <div part="body" class="watson-dialog__body">
                <div class="watson-list-group" id="result-list"></div>
            </div>
            <footer part="footer" class="watson-dialog__footer">
                <small>Version <span id="version"></span></small>
            </footer>
        </div>
    </div>
    
    <template id="result-header-template">
        <div class="result-header watson-list-group-header watson-sticky-top"></div>
    </template>
    
    <template id="result-item-template">
        <div class="result-item watson-list-group-item">
            <div class="watson-row">
                <div class="watson-col-auto">
                    <div class="result-icon"></div>
                </div>
                <div class="watson-col watson-text-truncate">
                    <div class="watson-text-body">
                        <span class="result-title"></span>
                        <span class="result-suffix watson-text-small"></span>
                    </div>
                    <div class="result-description watson-text-small">
                    
                    </div>
                </div>
            </div>
        </div>
    </template>
`;

Watson.Templates.Icon.innerHTML = `
    <style>
        :host {
            display: inline-block;
            width: 1em;
            height: 1em;
            box-sizing: content-box !important;
        }
        
        svg {
            display: block;
            height: 100%;
            width: 100%;
        }
    </style>
    
    <svg width="24" height="24" fill="currentColor" viewBox="0 0 24 24"><path d="M17.8 19.9s0-.1 0 0c-.1-.1-.1-.1-.1 0-.3.3-.7.8-1.3.9-.5.1-1.1 0-1.5-.4-.1-.1-.3-.2-.4-.3-.5-.4-1-.9-1.6-.8-.6 0-1 .5-1.3 1.1-.2-.6-.7-1-1.3-1.1-.7 0-1.1.4-1.6.8-.1.1-.3.2-.4.3-.5.4-1 .5-1.5.4-.6-.1-1.1-.6-1.3-.9h-.1s-.1 0-.1.1.3 1.2.9 1.8c.7.7 1.4 1.1 2.2 1.2.9.2 1.7 0 2.3-.4.4-.3.6-.6.8-1 .1.4.4.8.8 1 .4.3 1 .4 1.6.4.2 0 .5 0 .8-.1.8-.2 1.5-.6 2.2-1.2.6-.6.9-1.7.9-1.8zM5.5 8.6c-.3-.1-.6-.3-.9-.5-.2-.1-.3-.3-.4-.4 0-.1-.1-.1-.1-.1-.3-.4-.9-.5-1.4-.3-.4.3-.5.9-.2 1.3 0 0 .3.5.9.9.5.4 1.3.9 2.3.9h12.9c.8-.1 1.5-.5 2-.9.6-.5.9-.9.9-.9.3-.4.2-1-.2-1.3-.4-.3-1-.2-1.3.2l-.6.6c-.2.2-.5.3-.9.4"/><path d="M18.4 8.1V5.9C18.4 2.6 15.1 1 12 1 9 1 5.6 2.5 5.6 5.9v2.2h12.8zM20.3 18.7L18.5 17c.5-.7.7-1.5.7-2.4 0-2.4-2-4.4-4.5-4.4s-4.5 2-4.5 4.4c0 2.4 2 4.4 4.5 4.4.9 0 1.7-.3 2.4-.7L19 20c.2.2.4.3.7.3.5 0 .9-.4.9-.9 0-.3-.1-.5-.3-.7zm-5.6-.4c-2.1 0-3.8-1.7-3.8-3.7 0-2.1 1.7-3.7 3.8-3.7s3.8 1.7 3.8 3.7-1.7 3.7-3.8 3.7zM9 15.3l-2.1.3c-.3.1-.6-.2-.7-.6 0-.4.2-.7.5-.8l2.1-.3h.1c.3 0 .6.2.6.6 0 .4-.2.7-.5.8z"/><path d="M14.7 12.3c-1.5 0-2.7 1-2.7 2.2 0 1.2 1.2 2.2 2.7 2.2s2.7-1 2.7-2.2c0-1.2-1.2-2.2-2.7-2.2zm0 3.9c-1 0-1.9-.7-1.9-1.5s.8-1.5 1.9-1.5c1 0 1.9.7 1.9 1.5s-.9 1.5-1.9 1.5z"/><path d="M14.8 14.6c.6 0 1 .4 1 .8 0 .5-.5.8-1 .8-.6 0-1-.4-1-.8s.4-.8 1-.8z"/></svg>
`;

class WatsonIcon extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
    }
    connectedCallback() {
        this.shadowRoot.appendChild(Watson.Templates.Icon.content.cloneNode(true));
    }
}
customElements.define('watson-icon', WatsonIcon);


class WatsonAgent extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({ mode: 'open' });
        this.shadowRoot.appendChild(Watson.Templates.Agent.content.cloneNode(true));

        this.lastRexAccessKeyState = rex.accesskeys;
        this.keysPressed = new Set();
        this.settings = rex.watson;
        this.results = [];
        this.resultSelectedIndex = -1;

        this.navigationKeys = ['ArrowDown', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'Home', 'End', 'PageDown', 'PageUp'];

        const version = this.shadowRoot.getElementById('version');
        version.innerText = rex.watson.version;
    }

    connectedCallback() {
        window.addEventListener('keydown', (event) => {
            this.handleKeyDown(event);
        });
        window.addEventListener('keyup', (event) => {
            this.handleKeyUp(event);
        });
    }

    disconnectedCallback() {

    }

    toggleDialog() {
        if (this.hasAttribute('opened')) {
            this.hideDialog();
        }
        this.setAttribute('opened', '');

        this.lastRexAccessKeyState = rex.accesskeys;
        rex.accesskeys = false;
    }

    hideDialog() {
        if (this.hasAttribute('opened')) {
            this.removeAttribute('opened');
        }
        this.clearTextInput()
        this.clearResults()
        rex.accesskeys = this.lastRexAccessKeyState;
    }

    handleKeyDown(event) {
        if (event.key === "Tab") {
            event.preventDefault();
        }

        if (event.key === 'ArrowDown') {
            event.preventDefault();
            this.navigate(1);
            return;
        } else if (event.key === 'ArrowUp') {
            event.preventDefault();
            this.navigate(-1);
            return;
        } else if (event.key === 'Enter') {
            this.selectResult();
        }

        this.keysPressed.add(event.key);

        let isAlt, isControl, isEscape, isMeta, isSpace, isShift;
        isAlt = isControl = isEscape = isMeta = isSpace = isShift = false;

        this.keysPressed.forEach(key => {
            if (key === 'Alt') {
                isAlt = true;
            } else if (key === 'Control') {
                isControl = true;
            } else if (key === 'Escape') {
                isEscape = true;
            } else if (key === 'Meta') {
                isMeta = true;
            } else if (key === ' ') {
                isSpace = true;
            } else if (key === 'Shift') {
                isShift = true;
            }

            if (
                (this.settings.agentHotkey === '16-17-32' && isShift && isControl && isSpace) ||
                (this.settings.agentHotkey === '16-18-32' && isShift && isAlt && isSpace) ||
                (this.settings.agentHotkey === '17-18-32' && isControl && isAlt && isSpace) ||
                (this.settings.agentHotkey === '17-91-32' && isControl && isMeta && isSpace) ||
                (this.settings.agentHotkey === '16-32' && isShift && isSpace) ||
                (this.settings.agentHotkey === '17-32' && isControl && isSpace) ||
                (this.settings.agentHotkey === '18-32' && isAlt && isSpace)
            ) {
                this.toggleDialog();
                this.keysPressed.clear()
            } else if (isEscape) {
                this.hideDialog();
                this.keysPressed.clear()
            }
        })
    }

    handleKeyUp(event) {
        event.preventDefault()
        this.keysPressed.delete(event.key)

        if (this.navigationKeys.includes(event.key)) {
            return;
        }

        const textInput = this.shadowRoot.getElementById('text-input');

        if (!this.hasAttribute('opened')) {
            return;
        }
        if ('' === textInput.value || textInput.value.length <= 3) {
            return;
        }

        this.submitForm(textInput.value);
    }

    submitForm(inputValue) {
        const url = this.settings.backendRemoteUrl.replace('%QUERY', encodeURIComponent(inputValue));
        this.clearResults();

        fetch(url, {})
        .then(response => response.json())
        .then(data => {
            this.results = data;
            this.resultSelectedIndex = -1;
            this.renderResults();
        });
    }


    navigate(direction) {
        if (this.results.length === 0) return;

        this.resultSelectedIndex += direction;

        if (this.resultSelectedIndex < 0) {
            this.resultSelectedIndex = 0;
        } else if (this.resultSelectedIndex >= this.results.length) {
            this.resultSelectedIndex = this.results.length - 1;
        }

        this.renderResults();
        this.ensureVisible(this.resultSelectedIndex);
    }


    renderResults() {
        this.clearResults();
        const resultList = this.shadowRoot.getElementById('result-list');
        const resultHeaderTemplate = this.shadowRoot.getElementById('result-header-template');
        const resultItemTemplate = this.shadowRoot.getElementById('result-item-template');

        this.results.forEach((result, index) => {
            if (result.legend) {
                const cloneHeader = document.importNode(resultHeaderTemplate.content, true);
                const header = cloneHeader.querySelector('.result-header');
                header.textContent = result.legend;
                resultList.appendChild(cloneHeader);
            }

            const cloneItem = document.importNode(resultItemTemplate.content, true);
            const item = cloneItem.querySelector('.result-item');
            const icon = cloneItem.querySelector('.result-icon');
            const title = cloneItem.querySelector('.result-title');
            const suffix = cloneItem.querySelector('.result-suffix');
            const description = cloneItem.querySelector('.result-description');

            if (index === this.resultSelectedIndex) {
                item.classList.add('selected');
            }

            if (result.icon) {
                icon.classList.add(result.icon.trim());
            }
            if (result.value_name) {
                title.textContent = result.value_name;
            }
            if (result.value_suffix) {
                suffix.textContent = result.value_suffix;
            }
            if (result.description) {
                description.textContent = result.description;
            }

            resultList.appendChild(cloneItem);
        });
    }

    clearResults() {
        const resultList = this.shadowRoot.getElementById('result-list');
        resultList.innerHTML = '';
    }

    selectResult() {
        if (this.resultSelectedIndex >= 0 && this.resultSelectedIndex < this.results.length) {
            const resultItem = this.results[this.resultSelectedIndex];

            if (resultItem.url) {
                if (resultItem.url_open_window) {
                    window.open(resultItem.url, '_newtab');
                } else {
                    window.location.href = resultItem.url;
                }
            }
            this.results = [];
            this.resultSelectedIndex = -1;
            this.renderResults();
        }
    }

    ensureVisible(index) {
        const resultList = this.shadowRoot.getElementById('result-list');
        const resultListHeader = resultList.children[0];
        const resultListItem = resultList.querySelector('.result-item.selected');

        let headerRectDiff = 0;
        if (resultListHeader.classList.contains('result-header')) {
            const headerRect = resultListHeader.getBoundingClientRect();
            headerRectDiff = headerRect.bottom - headerRect.top;
        }

        if (resultListItem) {
            const listRect = resultList.getBoundingClientRect();
            const itemRect = resultListItem.getBoundingClientRect();
            if (itemRect.bottom > listRect.bottom) {
                resultList.scrollTop += itemRect.bottom - listRect.bottom;
            } else if (itemRect.top < listRect.top) {
                resultList.scrollTop -= listRect.top - itemRect.top + headerRectDiff;
            }
        }
    }

    clearTextInput() {
        const textInput = this.shadowRoot.getElementById('text-input');
        textInput.value = '';
    }
}
customElements.define('watson-agent', WatsonAgent);
