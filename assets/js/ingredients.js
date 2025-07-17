function addInged() {
    const ingredientsContainer = document.getElementById('ingredients');
    const ingredientCount = ingredientsContainer.querySelectorAll('.ingredient').length + 1;

    const ingredientDiv = document.createElement('div');
    ingredientDiv.className = 'ingredient';
    ingredientDiv.innerHTML = `
        <div class="ingredient">
            <input class="menge" type="text" id="ingredient${ingredientCount}" name="ingredient${ingredientCount}" required placeholder="Menge">
            <input class="zutat" type="text" id="ingredient${ingredientCount}" name="ingredient${ingredientCount}" required placeholder="Zutat">
                    <button type="button" class="remove-ingredient center" onclick="removeIngredient(this)" title="Löschen"><span class="material-symbols-outlined">close</span></button>
        </div>
    `;

    ingredientsContainer.appendChild(ingredientDiv);
}
function removeIngredient(button) {
    const ingredientDiv = button.parentElement;
    ingredientDiv.remove();
    
    // Update ingredient numbers
    const ingredients = document.querySelectorAll('.ingredient');
    ingredients.forEach((ingredient, index) => {
        ingredient.querySelector('label').textContent = `Zutat ${index + 1}:`;
        ingredient.querySelector('input').id = `ingredient${index + 1}`;
        ingredient.querySelector('input').name = `ingredient${index + 1}`;
    });

}