function addInged() {
    const ingredientsContainer = document.getElementById('ingredients');
    const ingredientCount = ingredientsContainer.querySelectorAll('.ingredient').length + 1;

    const ingredientDiv = document.createElement('div');
    ingredientDiv.className = 'ingredient';
    ingredientDiv.innerHTML = `
        <div class="ingredient">
            <input class="menge" name="menge[]" type="text" id="ingredient${ingredientCount}" name="ingredient${ingredientCount}" required placeholder="Menge">
            <select class="einheit" name="einheit[]"  required>
                        <option value=""disabled selected> </option>
                        <option value="g">g</option>
                        <option value="kg">kg</option>
                        <option value="ml">ml</option>
                        <option value="l">l</option>
                        <option value="EL">EL</option>
                        <option value="TL">TL</option>
                        <option value="%">%</option>
                        <option value="Stk.">Stk.</option>
                    </select>
            <input class="zutat" name="zutat[]" type="text" id="ingredient${ingredientCount}" name="ingredient${ingredientCount}" required placeholder="Zutat">
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