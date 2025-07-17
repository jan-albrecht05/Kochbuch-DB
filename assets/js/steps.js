function addStep(){
    const stepsContainer = document.getElementById('steps');
    const stepCount = stepsContainer.children.length + 1;
    
    const stepDiv = document.createElement('div');
    stepDiv.className = 'step';
    
    stepDiv.innerHTML = `
        <label class="schritt" for="schritt1">${stepCount}.</label>
        <textarea id="step-${stepCount}" name="step-${stepCount}" required></textarea>
        <button type="button" class="remove-step center" onclick="removeStep(this)" title="Löschen"><span class="material-symbols-outlined">close</span></button>
    `;
    
    stepsContainer.appendChild(stepDiv);
    if (stepCount > 9) {
        document.getElementById('add-step').style.display = 'none';
    }
}
function removeStep(button) {
    const stepDiv = button.parentElement;
    stepDiv.remove();
    // Update step numbers
    const steps = document.querySelectorAll('.step');  
    steps.forEach((step, index) => {
        step.querySelector('label').textContent = index + 1 + '.';
        step.querySelector('textarea').id = `schritt${index + 1}`;
        step.querySelector('textarea').name = `schritt${index + 1}`;
    });
    // Show the add step button if there are less than 10 steps
    if (steps.length < 10) {
        document.getElementById('add-step').style.display = 'flex';
    }
}