document.addEventListener("DOMContentLoaded", () => {

    const steps = [
        {
            element: ".chat-input",
            text: "Type your question here to chat with UniBot."
        },
        {
            element: ".btn-send",
            text: "Press SEND to submit your question. UniBot will answer below."
        },
        {
            element: ".new-chat-btn",
            text: "Click here to start a brand new conversation at any time."
        },
        {
            element: ".messages",
            text: "All your messages and UniBot replies will appear here."
        }
    ];

    let currentStep = 0;
    let overlay, highlightBox, tooltip, textBox;

    function createTutorialElements() {

        // --- Overlay ---
        overlay = document.createElement("div");
        overlay.style.cssText = `
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 9990;
        `;
        document.body.appendChild(overlay);

        // --- Highlight Box ---
        highlightBox = document.createElement("div");
        highlightBox.style.cssText = `
            position: absolute;
            border: 3px solid #4f8cff;
            border-radius: 12px;
            z-index: 9992;
            pointer-events: none;
        `;
        document.body.appendChild(highlightBox);

        // --- Tooltip container ---
        tooltip = document.createElement("div");
        tooltip.style.cssText = `
            position: absolute;
            background: #fff;
            padding: 16px;
            max-width: 260px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
            font-size: 15px;
            color: #111;
            z-index: 9993;
        `;
        document.body.appendChild(tooltip);

        // text container
        textBox = document.createElement("div");
        textBox.id = "tutorial-text-box";
        textBox.style.marginBottom = "10px";
        tooltip.appendChild(textBox);

        // Controls
        const controls = document.createElement("div");
        controls.style.cssText = "display:flex; gap:10px;";

        const nextBtn = document.createElement("button");
        nextBtn.innerText = "Next";
        nextBtn.style.cssText = `
            padding: 6px 12px;
            background: #4f8cff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor:pointer;
        `;
        nextBtn.addEventListener("click", nextStep);

        const closeBtn = document.createElement("button");
        closeBtn.innerText = "Close";
        closeBtn.style.cssText = `
            padding: 6px 12px;
            background: #999;
            color: white;
            border: none;
            border-radius: 8px;
            cursor:pointer;
        `;
        closeBtn.addEventListener("click", endTutorial);

        controls.appendChild(nextBtn);
        controls.appendChild(closeBtn);
        tooltip.appendChild(controls);
    }

    function startTutorial() {
        currentStep = 0;
        createTutorialElements();
        showStep();
    }

    function showStep() {
        const step = steps[currentStep];
        const target = document.querySelector(step.element);

        if (!target) {
            console.warn("Tutorial: Element not found:", step.element);
            nextStep();
            return;
        }

        const rect = target.getBoundingClientRect();

        // Highlight position
        highlightBox.style.top = rect.top - 6 + "px";
        highlightBox.style.left = rect.left - 6 + "px";
        highlightBox.style.width = rect.width + 12 + "px";
        highlightBox.style.height = rect.height + 12 + "px";

        // Tooltip text
        textBox.innerText = step.text;

        // Tooltip smart position
        let top = rect.bottom + 10;
        if (top + 120 > window.innerHeight) {
            top = rect.top - 140;
        }

        tooltip.style.top = top + "px";
        tooltip.style.left = rect.left + "px";
    }

    function nextStep() {
        currentStep++;
        if (currentStep >= steps.length) {
            endTutorial();
        } else {
            showStep();
        }
    }

    function endTutorial() {
        if (overlay) overlay.remove();
        if (highlightBox) highlightBox.remove();
        if (tooltip) tooltip.remove();
    }

    // Make startTutorial available globally for onclick=""
    window.startTutorial = startTutorial;

});
