/**
 * Multi Inventory - Product Page Integration
 */

class ProductInventory {
    constructor() {
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.updateInitialState();
    }

    setupEventListeners() {
        // Radio buttons
        const inventoryRadios = document.querySelectorAll('.inventory-radio');
        if (inventoryRadios.length > 0) {
            inventoryRadios.forEach(radio => {
                radio.addEventListener('change', () => this.handleInventoryChange(radio));
            });
        }

        // Dropdown selector
        const inventoryDropdown = document.querySelector('.inventory-dropdown');
        if (inventoryDropdown) {
            inventoryDropdown.addEventListener('change', () => this.handleDropdownChange(inventoryDropdown));
        }

        // Label selectors
        const inventoryLabels = document.querySelectorAll('.inventory-label');
        if (inventoryLabels.length > 0) {
            inventoryLabels.forEach(label => {
                label.addEventListener('click', () => this.handleLabelClick(label, inventoryLabels));
            });
        }

        // Add to cart form
        const addToCartForm = document.querySelector('form.add-to-cart-form');
        if (addToCartForm) {
            addToCartForm.addEventListener('submit', (e) => this.validateInventorySelection(e, addToCartForm));
        }
    }

    updateInitialState() {
        // Trigger the selected inventory option to update the UI
        const selectedRadio = document.querySelector('.inventory-radio:checked');
        if (selectedRadio) {
            this.handleInventoryChange(selectedRadio);
        }

        const inventoryDropdown = document.querySelector('.inventory-dropdown');
        if (inventoryDropdown) {
            this.handleDropdownChange(inventoryDropdown);
        }

        const selectedLabel = document.querySelector('.inventory-label.selected');
        if (selectedLabel) {
            // No need to pass the full collection here as we're not changing selection
            this.handleLabelClick(selectedLabel, null, false);
        } else {
            // If no label is selected, select the first one by default
            const firstLabel = document.querySelector('.inventory-label');
            if (firstLabel) {
                const allLabels = document.querySelectorAll('.inventory-label');
                this.handleLabelClick(firstLabel, allLabels);
            }
        }
    }

    handleInventoryChange(radio) {
        const inventoryId = radio.value;
        const stock = parseInt(radio.dataset.stock);
        const price = radio.dataset.price ? parseFloat(radio.dataset.price) : null;
        
        this.updateProductState(inventoryId, stock, price);
    }

    handleDropdownChange(dropdown) {
        const selectedOption = dropdown.options[dropdown.selectedIndex];
        const inventoryId = selectedOption.value;
        const stock = parseInt(selectedOption.dataset.stock);
        const price = selectedOption.dataset.price ? parseFloat(selectedOption.dataset.price) : null;
        
        this.updateProductState(inventoryId, stock, price);
        
        // Update the displayed inventory info if present
        const infoContainer = document.querySelector('.inventory-dropdown-info');
        if (infoContainer) {
            // Clear previous info
            infoContainer.innerHTML = '';
            
            // Add stock badge
            if (stock <= 0) {
                infoContainer.innerHTML += `<span class="badge bg-danger">${window.translations.outOfStock || 'Out of Stock'}</span>`;
            } else {
                infoContainer.innerHTML += `<span class="badge bg-success">${window.translations.inStock || 'In Stock'}</span>`;
                
                // Show stock count if enabled
                if (window.inventorySettings?.showStockCount) {
                    infoContainer.innerHTML += ` (${stock})`;
                }
            }
            
            // Add delivery time if available
            if (selectedOption.dataset.deliveryTime) {
                infoContainer.innerHTML += `<div class="delivery-time">${selectedOption.dataset.deliveryTime}</div>`;
            }
        }
    }

    handleLabelClick(clickedLabel, allLabels, updateSelection = true) {
        if (updateSelection && allLabels) {
            // Remove selected class from all labels
            allLabels.forEach(label => label.classList.remove('selected'));
            
            // Add selected class to clicked label
            clickedLabel.classList.add('selected');
        }
        
        // Get inventory data
        const inventoryId = clickedLabel.dataset.inventory;
        const stock = parseInt(clickedLabel.dataset.stock);
        const price = clickedLabel.dataset.price ? parseFloat(clickedLabel.dataset.price) : null;
        
        // Update hidden input if it exists
        const hiddenInput = document.querySelector('input[name="inventory_id"]');
        if (hiddenInput) {
            hiddenInput.value = inventoryId;
        }
        
        this.updateProductState(inventoryId, stock, price);
    }

    updateProductState(inventoryId, stock, price) {
        // Update price if different from original
        this.updatePrice(price);
        
        // Update stock status and add-to-cart button
        this.updateStockStatus(stock);
        
        // Save selected inventory in session
        this.saveSelectedInventory(inventoryId);
    }

    updatePrice(price) {
        if (!price) return;
        
        const priceElement = document.querySelector('.product-price');
        const originalPriceElement = document.querySelector('.original-price');
        const originalPrice = priceElement?.dataset?.originalPrice ? 
            parseFloat(priceElement.dataset.originalPrice) : 
            null;
            
        if (priceElement && originalPrice !== null) {
            if (price !== originalPrice) {
                // Update displayed price
                priceElement.innerHTML = this.formatPrice(price);
                
                // Show original price as strikethrough if different
                if (originalPriceElement) {
                    originalPriceElement.innerHTML = this.formatPrice(originalPrice);
                    originalPriceElement.style.display = 'inline-block';
                }
            } else {
                // Reset to original price
                priceElement.innerHTML = this.formatPrice(originalPrice);
                
                if (originalPriceElement) {
                    originalPriceElement.style.display = 'none';
                }
            }
        }
    }

    updateStockStatus(stock) {
        const addToCartBtn = document.querySelector('.add-to-cart-button');
        const stockStatus = document.querySelector('.stock-status');
        
        if (addToCartBtn) {
            if (stock <= 0) {
                addToCartBtn.disabled = true;
                addToCartBtn.classList.add('disabled');
            } else {
                addToCartBtn.disabled = false;
                addToCartBtn.classList.remove('disabled');
            }
        }
        
        if (stockStatus) {
            if (stock <= 0) {
                stockStatus.innerHTML = `<span class="badge bg-danger">${window.translations.outOfStock || 'Out of Stock'}</span>`;
            } else {
                const displayMode = window.inventorySettings?.stockDisplay || 'count';
                
                if (displayMode === 'count') {
                    stockStatus.innerHTML = `<span class="badge bg-success">${window.translations.inStock || 'In Stock'} (${stock})</span>`;
                } else {
                    stockStatus.innerHTML = `<span class="badge bg-success">${window.translations.inStock || 'In Stock'}</span>`;
                }
            }
        }
    }

    saveSelectedInventory(inventoryId) {
        if (!inventoryId) return;
        
        fetch('/set-selected-inventory', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                inventory_id: inventoryId
            })
        });
    }

    validateInventorySelection(e, form) {
        const inventoryRequired = window.inventorySettings?.inventoryRequired === undefined ? 
            true : 
            window.inventorySettings.inventoryRequired;
            
        if (!inventoryRequired) {
            return true;
        }
        
        let inventoryId = null;
        
        // Check for radio buttons
        const selectedRadio = form.querySelector('.inventory-radio:checked');
        if (selectedRadio) {
            inventoryId = selectedRadio.value;
        }
        
        // Check for dropdown
        const dropdown = form.querySelector('.inventory-dropdown');
        if (dropdown) {
            inventoryId = dropdown.value;
        }
        
        // Check for hidden input (used with label selection)
        const hiddenInput = form.querySelector('input[name="inventory_id"]');
        if (hiddenInput && hiddenInput.value) {
            inventoryId = hiddenInput.value;
        }
        
        if (!inventoryId) {
            e.preventDefault();
            this.showError(window.translations.noInventorySelected || 'Please select an inventory before adding to cart.');
            return false;
        }
        
        return true;
    }

    showError(message) {
        // Check for Botble.showError function first
        if (typeof Botble !== 'undefined' && typeof Botble.showError === 'function') {
            Botble.showError(message);
        } else {
            // Fallback to alert if Botble is not available
            alert(message);
        }
    }

    formatPrice(price) {
        const currencySettings = window.currencySettings || {
            symbol: '$',
            decimal: '.',
            thousands: ',',
            precision: 2,
            position: 'left'
        };
        
        // Format the number
        let formattedPrice = price.toFixed(currencySettings.precision);
        
        // Add thousands separator
        formattedPrice = formattedPrice.replace(/\B(?=(\d{3})+(?!\d))/g, currencySettings.thousands);
        
        // Add currency symbol in the correct position
        if (currencySettings.position === 'left') {
            return currencySettings.symbol + formattedPrice;
        } else {
            return formattedPrice + currencySettings.symbol;
        }
    }
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    window.productInventory = new ProductInventory();
});