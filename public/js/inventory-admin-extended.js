/**
 * Multi Inventory Plugin - Extended Admin Functionality
 */

class MultiInventoryAdmin {
    constructor() {
        this.init();
    }

    init() {
        this.setupSelects();
        this.setupStockUpdates();
        this.setupBulkUpdates();
        this.setupDataTable();
        this.setupInventoryMap();
        this.setupInventoryDefaults();
        this.setupImportExport();
        this.setupSettingsPage();
        this.setupProductPage();
    }

    setupSelects() {
        // Enable select2 for multiple selects
        const multiSelects = document.querySelectorAll('.taxonomy-inventories select[multiple], #multi-inventory-inventories');
        multiSelects.forEach(select => {
            if (jQuery && jQuery().select2) {
                jQuery(select).select2();
            }
        });
    }

    setupStockUpdates() {
        const inventoryManagerTable = document.querySelector('.multi-inventory-manager-table');
        if (!inventoryManagerTable) {
            return;
        }

        const overlay = document.querySelector('.multi-inventory-manager-table-spinner-overlay');
        
        // Initialize DataTable if available
        if (jQuery && jQuery().dataTable) {
            jQuery(inventoryManagerTable).dataTable({
                "pageLength": 200,
                "order": [[3, "asc"]],
                "paging": false
            });
        }

        // Setup debounce function for stock updates
        const debounce = (callback, delay = 600) => {
            let timer;
            return (...args) => {
                clearTimeout(timer);
                timer = setTimeout(() => callback(...args), delay);
            };
        };

        let previousValues = {};

        // Handle stock input changes
        document.addEventListener('change', e => {
            if (!e.target.matches('.multi-inventory-manager-table-stock')) {
                return;
            }

            e.preventDefault();
            const input = e.target;
            const productId = input.dataset.productId;
            const inventoryId = input.dataset.inventoryId;
            const stock = input.value;

            // Skip if value hasn't changed
            const key = `${productId}-${inventoryId}`;
            if (previousValues[key] === stock) {
                return;
            }
            previousValues[key] = stock;

            // Show loading overlay
            if (overlay) {
                overlay.style.display = 'block';
            }

            // Send update via Ajax
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('product_id', productId);
            formData.append('inventory_id', inventoryId);
            formData.append('stock', stock);

            fetch('/admin/multi-inventory/update-stock', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(response => {
                if (!response.error) {
                    this.updateTableTotals(input);
                } else {
                    alert('Error: ' + response.message);
                }
                
                if (overlay) {
                    overlay.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error updating stock:', error);
                if (overlay) {
                    overlay.style.display = 'none';
                }
            });
        }, true);

        // Use debounced version for keyup events to reduce server load
        const debouncedUpdate = debounce((e) => {
            if (!e.target.matches('.multi-inventory-manager-table-stock')) {
                return;
            }
            
            e.target.dispatchEvent(new Event('change'));
        }, 600);

        document.addEventListener('keyup', debouncedUpdate);
    }

    updateTableTotals(input) {
        const row = input.closest('tr');
        
        // Update frontend stock total
        if (input.classList.contains('multi-inventory-manager-table-frontend-stock')) {
            let frontendStock = 0;
            const frontendStocks = row.querySelectorAll('.multi-inventory-manager-table-frontend-stock');
            frontendStocks.forEach(stockInput => {
                frontendStock += parseInt(stockInput.value) || 0;
            });
            
            const totalFrontendStockInput = row.querySelector('.multi-inventory-manager-table-total-frontend-stock');
            if (totalFrontendStockInput) {
                totalFrontendStockInput.value = frontendStock;
                totalFrontendStockInput.dispatchEvent(new Event('keyup'));
            }
        }

        // Update backend stock total
        if (input.classList.contains('multi-inventory-manager-table-backend-stock')) {
            let backendStock = 0;
            const backendStocks = row.querySelectorAll('.multi-inventory-manager-table-backend-stock');
            backendStocks.forEach(stockInput => {
                backendStock += parseInt(stockInput.value) || 0;
            });
            
            const totalBackendStockInput = row.querySelector('.multi-inventory-manager-table-total-backend-stock');
            if (totalBackendStockInput) {
                totalBackendStockInput.value = backendStock;
            }
        }

        // Update total inventory stock
        if (input.classList.contains('multi-inventory-manager-table-inventory-stock')) {
            let totalStock = 0;
            const stocks = row.querySelectorAll('.multi-inventory-manager-table-inventory-stock');
            stocks.forEach(stockInput => {
                totalStock += parseInt(stockInput.value) || 0;
            });
            
            const totalStockInput = row.querySelector('.multi-inventory-manager-table-total-stock');
            if (totalStockInput) {
                totalStockInput.value = totalStock;
            }
        }
    }

    setupBulkUpdates() {
        // Handle bulk update actions
        document.addEventListener('click', e => {
            if (!e.target.matches('.bulk-update-btn')) {
                return;
            }
            
            e.preventDefault();
            const btn = e.target;
            const action = btn.dataset.action;
            const inputId = btn.dataset.input;
            const inventoryId = btn.dataset.inventory;
            
            const valueInput = document.getElementById(inputId);
            if (!valueInput) {
                return;
            }
            
            const value = parseFloat(valueInput.value) || 0;
            
            // Find all inputs to update
            const selector = inventoryId ? 
                `input[name^="inventories[${inventoryId}]"]` : 
                'input[name^="inventories"][name$="[stock]"], input[name^="inventories"][name$="[price]"]';
            
            const inputs = document.querySelectorAll(selector);
            
            inputs.forEach(input => {
                let currentValue = parseFloat(input.value) || 0;
                let newValue = currentValue;
                
                switch (action) {
                    case 'set':
                        newValue = value;
                        break;
                    case 'increase':
                        newValue = currentValue + value;
                        break;
                    case 'decrease':
                        newValue = Math.max(0, currentValue - value);
                        break;
                    case 'percentage_increase':
                        newValue = currentValue * (1 + (value / 100));
                        break;
                    case 'percentage_decrease':
                        newValue = currentValue * (1 - (value / 100));
                        break;
                }
                
                // Ensure stock values are not negative
                if (input.name.includes('[stock]')) {
                    newValue = Math.max(0, Math.round(newValue));
                } else if (input.name.includes('[price]')) {
                    newValue = Math.max(0, parseFloat(newValue.toFixed(2)));
                }
                
                input.value = newValue;
                
                // Trigger change event to update totals and save
                input.dispatchEvent(new Event('change'));
            });
        });
    }

    setupDataTable() {
        const dataTableContainers = document.querySelectorAll('.multi-inventory-datatable');
        
        if (dataTableContainers.length === 0 || !jQuery || !jQuery().dataTable) {
            return;
        }
        
        dataTableContainers.forEach(container => {
            jQuery(container).dataTable({
                responsive: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "_MENU_ records per page",
                    info: "Showing _START_ to _END_ of _TOTAL_ records",
                }
            });
        });
    }

    setupInventoryMap() {
        const mapContainer = document.getElementById('coordinate-picker-map');
        if (!mapContainer || typeof google === 'undefined' || !google.maps) {
            return;
        }

        const latInput = document.querySelector('input[name="latitude"]');
        const lngInput = document.querySelector('input[name="longitude"]');
        
        if (!latInput || !lngInput) {
            return;
        }

        // Initialize with current lat/lng or default to a central location
        const lat = parseFloat(latInput.value) || 40.7128;
        const lng = parseFloat(lngInput.value) || -74.0060;
        
        const mapOptions = {
            center: { lat, lng },
            zoom: 12
        };
        
        const map = new google.maps.Map(mapContainer, mapOptions);
        
        // Add marker at current position
        let marker = new google.maps.Marker({
            position: { lat, lng },
            map: map,
            draggable: true
        });
        
        // Update inputs when marker is dragged
        google.maps.event.addListener(marker, 'dragend', function() {
            const position = marker.getPosition();
            latInput.value = position.lat().toFixed(7);
            lngInput.value = position.lng().toFixed(7);
        });
        
        // Add click event to map to reposition marker
        google.maps.event.addListener(map, 'click', function(event) {
            marker.setPosition(event.latLng);
            latInput.value = event.latLng.lat().toFixed(7);
            lngInput.value = event.latLng.lng().toFixed(7);
        });
        
        // Update map when inputs change
        [latInput, lngInput].forEach(input => {
            input.addEventListener('change', function() {
                const lat = parseFloat(latInput.value);
                const lng = parseFloat(lngInput.value);
                
                if (!isNaN(lat) && !isNaN(lng)) {
                    const newPosition = { lat, lng };
                    marker.setPosition(newPosition);
                    map.setCenter(newPosition);
                }
            });
        });
        
        // Handle geocode button click
        const geocodeBtn = document.getElementById('geocode-address-btn');
        if (geocodeBtn) {
            geocodeBtn.addEventListener('click', e => {
                e.preventDefault();
                
                const addressInput = document.querySelector('input[name="address"]');
                const cityInput = document.querySelector('input[name="city"]');
                const stateInput = document.querySelector('input[name="state"]');
                const zipInput = document.querySelector('input[name="zip_code"]');
                
                if (!addressInput) {
                    return;
                }
                
                const geocoder = new google.maps.Geocoder();
                const address = [
                    addressInput.value,
                    cityInput ? cityInput.value : '',
                    stateInput ? stateInput.value : '',
                    zipInput ? zipInput.value : ''
                ].filter(Boolean).join(', ');
                
                if (!address) {
                    alert('Please enter an address to geocode');
                    return;
                }
                
                geocoder.geocode({ 'address': address }, function(results, status) {
                    if (status === google.maps.GeocoderStatus.OK) {
                        const location = results[0].geometry.location;
                        
                        map.setCenter(location);
                        marker.setPosition(location);
                        
                        latInput.value = location.lat().toFixed(7);
                        lngInput.value = location.lng().toFixed(7);
                    } else {
                        alert('Geocode was not successful for the following reason: ' + status);
                    }
                });
            });
        }
    }

    setupInventoryDefaults() {
        // Handle default inventory toggle
        const defaultToggle = document.querySelector('input[name="is_default"]');
        if (defaultToggle) {
            defaultToggle.addEventListener('change', function() {
                if (this.checked) {
                    if (!confirm('Making this the default inventory will remove default status from any other inventory. Continue?')) {
                        this.checked = false;
                    }
                }
            });
        }

        // Form validation for inventory creation/edit
        const inventoryForm = document.getElementById('inventory-form');
        if (inventoryForm) {
            inventoryForm.addEventListener('submit', e => {
                const nameInput = inventoryForm.querySelector('input[name="name"]');
                if (!nameInput || !nameInput.value.trim()) {
                    e.preventDefault();
                    alert('Inventory name is required');
                    return;
                }

                const latInput = inventoryForm.querySelector('input[name="latitude"]');
                const lngInput = inventoryForm.querySelector('input[name="longitude"]');

                if (latInput && latInput.value) {
                    const lat = parseFloat(latInput.value);
                    if (isNaN(lat) || lat < -90 || lat > 90) {
                        e.preventDefault();
                        alert('Latitude must be between -90 and 90');
                        return;
                    }
                }

                if (lngInput && lngInput.value) {
                    const lng = parseFloat(lngInput.value);
                    if (isNaN(lng) || lng < -180 || lng > 180) {
                        e.preventDefault();
                        alert('Longitude must be between -180 and 180');
                        return;
                    }
                }
            });
        }
    }

    setupImportExport() {
        // Validate import form
        const importForm = document.getElementById('inventory-import-form');
        if (importForm) {
            importForm.addEventListener('submit', e => {
                const fileInput = importForm.querySelector('input[type="file"]');
                if (!fileInput || !fileInput.files.length) {
                    e.preventDefault();
                    alert('Please select a file to import');
                    return;
                }

                const fileExt = fileInput.files[0].name.split('.').pop().toLowerCase();
                if (!['xlsx', 'xls', 'csv'].includes(fileExt)) {
                    e.preventDefault();
                    alert('Please select a valid Excel or CSV file');
                    return;
                }
            });
        }

        // Handle batch import actions
        const batchActions = document.querySelector('.batch-import-actions');
        if (batchActions) {
            batchActions.addEventListener('click', e => {
                if (!e.target.matches('.batch-import-action')) {
                    return;
                }

                e.preventDefault();
                const action = e.target.dataset.action;
                const form = document.getElementById('batch-import-form');

                if (!form) {
                    return;
                }

                const actionInput = form.querySelector('input[name="action"]');
                if (actionInput) {
                    actionInput.value = action;
                }

                // Ask for confirmation based on action
                if (action === 'overwrite' && !confirm('This will overwrite existing stock data. Continue?')) {
                    return;
                }

                form.submit();
            });
        }
    }

    setupSettingsPage() {
        // Toggle conditional fields in settings
        const toggleRadios = {
            'click_collect_enabled': '.delivery-inventory-container',
            'modify_stock_quantity': '.stock-quantity-options',
            'display_type': {
                'radio': '.radio-options',
                'select': '.select-options',
                'label': '.label-options',
                'hidden': '.hidden-options'
            },
            'order_flow': {
                'custom': '.custom-order-options',
                'country': '.country-order-options'
            },
            'stock_display': {
                'count': '.count-display-options',
                'inout': '.inout-display-options'
            }
        };

        // Handle radio toggles
        Object.keys(toggleRadios).forEach(radioName => {
            const radios = document.querySelectorAll(`input[name="${radioName}"]`);
            if (radios.length === 0) {
                return;
            }

            radios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const value = this.value;
                    const target = toggleRadios[radioName];

                    if (typeof target === 'string') {
                        // Simple toggle
                        const container = document.querySelector(target);
                        if (container) {
                            container.style.display = value === '1' ? 'block' : 'none';
                        }
                    } else if (typeof target === 'object') {
                        // Multiple options toggle
                        Object.keys(target).forEach(optionValue => {
                            const container = document.querySelector(target[optionValue]);
                            if (container) {
                                container.style.display = value === optionValue ? 'block' : 'none';
                            }
                        });
                    }
                });
            });

            // Trigger initial state
            const checkedRadio = document.querySelector(`input[name="${radioName}"]:checked`);
            if (checkedRadio) {
                checkedRadio.dispatchEvent(new Event('change'));
            }
        });

        // Google Maps API Key validation
        const apiKeyInput = document.querySelector('input[name="google_maps_api_key"]');
        const mapFeaturesToggle = document.querySelector('input[name="map_features_enabled"]');
        
        if (apiKeyInput && mapFeaturesToggle) {
            const toggleMapFeatures = () => {
                const mapFeatures = document.querySelector('.map-features-container');
                if (mapFeatures) {
                    mapFeatures.style.display = mapFeaturesToggle.checked && apiKeyInput.value ? 'block' : 'none';
                }
            };
            
            apiKeyInput.addEventListener('input', toggleMapFeatures);
            mapFeaturesToggle.addEventListener('change', toggleMapFeatures);
            
            // Initial check
            toggleMapFeatures();
        }
    }

    setupProductPage() {
        // Handle inventory pricing toggle on product edit page
        const enablePricingToggle = document.querySelector('input[name="enable_inventory_pricing"]');
        if (enablePricingToggle) {
            const togglePricingFields = () => {
                const pricingFields = document.querySelectorAll('.inventory-price-field');
                pricingFields.forEach(field => {
                    field.style.display = enablePricingToggle.checked ? 'block' : 'none';
                });
            };
            
            enablePricingToggle.addEventListener('change', togglePricingFields);
            
            // Initial state
            togglePricingFields();
        }

        // Handle low stock threshold
        const lowStockThresholdToggle = document.querySelector('input[name="enable_low_stock_threshold"]');
        const lowStockThresholdInput = document.querySelector('input[name="low_stock_threshold"]');
        
        if (lowStockThresholdToggle && lowStockThresholdInput) {
            lowStockThresholdToggle.addEventListener('change', function() {
                lowStockThresholdInput.disabled = !this.checked;
            });
            
            // Initial state
            lowStockThresholdInput.disabled = !lowStockThresholdToggle.checked;
        }

        // Setup tabs in product inventory panel
        const inventoryTabs = document.querySelectorAll('.inventory-tab-link');
        if (inventoryTabs.length > 0) {
            inventoryTabs.forEach(tab => {
                tab.addEventListener('click', e => {
                    e.preventDefault();
                    
                    // Remove active class from all tabs
                    inventoryTabs.forEach(t => t.classList.remove('active'));
                    
                    // Add active class to clicked tab
                    tab.classList.add('active');
                    
                    // Hide all tab contents
                    const tabContents = document.querySelectorAll('.inventory-tab-content');
                    tabContents.forEach(content => content.style.display = 'none');
                    
                    // Show selected tab content
                    const targetId = tab.getAttribute('href').substring(1);
                    const targetContent = document.getElementById(targetId);
                    if (targetContent) {
                        targetContent.style.display = 'block';
                    }
                });
            });
            
            // Activate first tab by default
            inventoryTabs[0].click();
        }
    }
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    window.multiInventoryAdminExtended = new MultiInventoryAdmin();
});