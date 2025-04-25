/**
 * Multi Inventory Plugin - Inventory Locations and Selection
 */

class InventoryLocations {
    constructor(options) {
        this.options = Object.assign({
            popupEnable: true,
            popupShowAutomatically: false,
            popupDisableGeolocation: false,
            popupShowSearchAutocomplete: true,
            popupMiles: false,
            googleAPIKey: '',
            productPageStockDisplay: 'count',
            productPageDisplay: 'radio',
            defaultInventory: 0,
            texts: {
                inStock: 'In Stock',
                outOfStock: 'Out of Stock',
                leftInStock: '%s left in stock'
            },
            ajax_url: '/multi-inventory/ajax'
        }, options);

        this.init();
    }

    init() {
        this.overlay = document.getElementById('multi-inventory-overlay');
        this.popup = document.getElementById('multi-inventory-popup-container');
        this.nearestLocationLoader = document.querySelector('.multi-inventory-popup-locations-nearest-location-loader');
        this.allLocationsContainer = document.querySelector('.multi-inventory-popup-locations-container');
        this.nearestLocationError = document.querySelector('.multi-inventory-popup-locations-nearest-location-error');
        this.nearestLocation = document.querySelector('.multi-inventory-popup-locations-nearest-location');
        this.deliveryLocationContainer = document.querySelector('.multi-inventory-popup-locations-delivery-location-container');
        
        this.window = window;
        this.currentURL = window.location.href;
        this.documentHeight = document.documentElement.scrollHeight;
        this.windowHeight = window.innerHeight;
        this.products = {};

        // Initialize Google geocoder if API key is available
        if (typeof google !== "undefined" && this.options.googleAPIKey) {
            this.geocoder = new google.maps.Geocoder();
        }

        this.setupEventListeners();
        this.maybeChangeLinks();
        this.setupPopup();
        this.setupVariationStock();
        this.setupChangeInventory();
        this.setupPopupInventorySearch();
        this.setupLabelPopup();
    }

    setupEventListeners() {
        // Open inventory popup
        document.addEventListener('click', (e) => {
            if (e.target.matches('.multi-inventory-open-popup, .multi-inventory-cart-switch-inventory-button')) {
                e.preventDefault();
                this.productId = e.target.dataset.productId;
                this.popupOpen();
            }
        });

        // Close inventory popup when clicking overlay
        if (this.overlay) {
            this.overlay.addEventListener('click', () => {
                this.overlay.style.display = 'none';
                this.popup.style.display = 'none';
            });
        }

        // Close inventory popup button
        document.addEventListener('click', (e) => {
            if (e.target.matches('.multi-inventory-popup-close-container') || e.target.closest('.multi-inventory-popup-close-container')) {
                e.preventDefault();
                this.overlay.style.display = 'none';
                this.popup.style.display = 'none';
            }
        });

        // Choose inventory location
        document.addEventListener('click', (e) => {
            if (e.target.matches('.multi-inventory-choose-location') || e.target.closest('.multi-inventory-choose-location')) {
                e.preventDefault();
                
                const locationElements = document.querySelectorAll('.multi-inventory-choose-location');
                locationElements.forEach(el => el.classList.remove('multi-inventory-choose-location-selected'));
                
                const clickedElement = e.target.matches('.multi-inventory-choose-location') ? 
                    e.target : e.target.closest('.multi-inventory-choose-location');
                
                const inventoryId = clickedElement.dataset.id;
                const inventoryName = clickedElement.dataset.name;
                
                const selectedLocationElement = document.querySelector('.multi-inventory-selected-location');
                if (selectedLocationElement) {
                    selectedLocationElement.textContent = inventoryName;
                }

                this.saveCookie('multi_inventory_inventory', inventoryId, 365);
                this.saveCookie('multi_inventory_inventory_name', inventoryName, 365);

                clickedElement.classList.add('multi-inventory-choose-location-selected');
                
                this.overlay.style.display = 'none';
                this.popup.style.display = 'none';

                // Redirect with inventory parameter
                window.location.href = this.addOrUpdateUrlParam(window.location.href, 'inventory', inventoryId);
            }
        });
    }

    maybeChangeLinks() {
        if (this.options.disableStateReplace === "1") {
            return;
        }

        let currentInventory = this.getParameterByName('inventory');
        if (!currentInventory) {
            currentInventory = this.readCookie('multi_inventory_inventory');
            if (!currentInventory) {
                return;
            }
        }

        // Add inventory parameter to all links
        document.querySelectorAll('a').forEach(link => {
            const href = link.getAttribute('href');
            if (!href || href.includes('#') || href.includes('tel:') || 
                href.includes('?inventory') || href.includes('mailto:')) {
                return;
            }

            link.setAttribute('href', 
                href + (href.includes('?') ? `&inventory=${currentInventory}` : `?inventory=${currentInventory}`)
            );
        });
    }

    setupPopup() {
        const existingInventory = this.readCookie('multi_inventory_inventory');
        if (existingInventory) {
            const existingInventoryName = this.readCookie('multi_inventory_inventory_name');
            if (existingInventoryName) {
                const selectedLocationElements = document.querySelectorAll('.multi-inventory-selected-location');
                selectedLocationElements.forEach(el => {
                    el.textContent = existingInventoryName;
                });
            }
        }

        if (!this.popup) {
            return;
        }

        const popupShowed = this.readCookie('multi_inventory_popup');
        if (popupShowed || this.options.popupEnable !== "1" || this.options.popupShowAutomatically !== "1") {
            return;
        }

        this.popupOpen();
    }

    setupPopupInventorySearch() {
        // Search with Enter key
        document.addEventListener('keyup', (e) => {
            if (e.target.matches('.multi-inventory-popup-address') && e.key === 'Enter') {
                const searchButton = document.querySelector('.multi-inventory-popup-address-button');
                if (searchButton) {
                    searchButton.click();
                }
            }
        });

        // Search button click
        document.addEventListener('click', (e) => {
            if (e.target.matches('.multi-inventory-popup-address-button')) {
                e.preventDefault();
                
                const addressField = document.querySelector('.multi-inventory-popup-address');
                if (!addressField || !this.geocoder) {
                    return;
                }
                
                const address = {
                    address: addressField.value
                };

                this.geocoder.geocode(address, (results, status) => {
                    if (status === google.maps.GeocoderStatus.OK) {
                        const geometryLocation = results[0].geometry.location;
                        this.getInventories(geometryLocation.lat(), geometryLocation.lng());
                    }
                });
            }
        });

        // Setup Google Places autocomplete
        if (this.options.popupShowSearchAutocomplete === "1" && typeof google !== "undefined" && 
            typeof google.maps !== "undefined" && typeof google.maps.places !== "undefined") {
            
            const addressField = document.querySelector('.multi-inventory-popup-address');
            if (addressField) {
                const autocomplete = new google.maps.places.Autocomplete(addressField, {
                    fields: ["name", "geometry.location", "place_id", "formatted_address"],
                    types: ['geocode']
                });
                
                autocomplete.addListener('place_changed', () => {
                    const place = autocomplete.getPlace();
                    if (place.formatted_address) {
                        const address = {
                            address: place.formatted_address
                        };

                        this.geocoder.geocode(address, (results, status) => {
                            if (status === google.maps.GeocoderStatus.OK) {
                                const geometryLocation = results[0].geometry.location;
                                this.getInventories(geometryLocation.lat(), geometryLocation.lng());
                            }
                        });
                    }
                });
            }
        }
    }

    setupLabelPopup() {
        document.addEventListener('click', (e) => {
            // Delivery option clicked
            if (e.target.matches('.multi-inventory-inventories-delivery-container') || 
                e.target.closest('.multi-inventory-inventories-delivery-container')) {
                
                const clickCollectInputs = document.querySelectorAll('.multi-inventory-inventories-click-collect-container input');
                clickCollectInputs.forEach(input => {
                    input.checked = false;
                });
                
                // Redirect with inventory on checkout page
                if (document.querySelector('.checkout-form')) {
                    const deliveryInput = document.querySelector('.multi-inventory-inventories-delivery-container input[name="multi_inventory_inventory"]');
                    if (deliveryInput) {
                        const deliveryInventoryId = deliveryInput.value;
                        const url = window.location.href.split('?')[0];
                        window.location.href = `${url}?inventory=${deliveryInventoryId}`;
                    }
                }
            }
            
            // Click & Collect option clicked
            if (e.target.matches('.multi-inventory-inventories-click-collect-container') || 
                e.target.closest('.multi-inventory-inventories-click-collect-container')) {
                
                const deliveryInputs = document.querySelectorAll('.multi-inventory-inventories-delivery-container input');
                deliveryInputs.forEach(input => {
                    input.checked = false;
                });
                
                const clickedContainer = e.target.matches('.multi-inventory-inventories-click-collect-container') ? 
                    e.target : e.target.closest('.multi-inventory-inventories-click-collect-container');
                
                const fakeInput = clickedContainer.querySelector('input[name="multi_inventory_fake"]');
                if (fakeInput) {
                    fakeInput.checked = true;
                }
                
                // Open popup for inventory selection
                if (document.querySelector('.multi-inventory-inventories-layout-labelPopup')) {
                    this.popupOpen();
                }
            }
        });
    }

    popupOpen(showInventories = null) {
        if (!this.overlay || !this.popup) {
            return;
        }

        if (this.nearestLocation) {
            this.nearestLocation.innerHTML = '';
        }
        
        if (this.nearestLocationLoader) {
            this.nearestLocationLoader.style.display = 'block';
        }
        
        if (this.allLocationsContainer) {
            this.allLocationsContainer.style.display = 'none';
        }
        
        if (this.deliveryLocationContainer) {
            this.deliveryLocationContainer.style.display = 'none';
        }

        const searchField = document.querySelector('.multi-inventory-popup-address');

        // Show all location elements or only specific ones
        const locationElements = document.querySelectorAll('.multi-inventory-popup-all-locations-location');
        locationElements.forEach(el => {
            el.style.display = 'block';
        });

        if (this.options.popupDisableGeolocation === "1") {
            this.getInventories(null, null);
        } else {
            // Try to get user location
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Try to get address from coordinates for search field
                    if (searchField && this.geocoder) {
                        const latlng = {lat, lng};
                        this.geocoder.geocode({'location': latlng}, (results, status) => {
                            if (status === google.maps.GeocoderStatus.OK) {
                                if (results[1]) {
                                    searchField.value = results[1].formatted_address;
                                }
                            }
                        });
                    }

                    this.saveCookie('multi_inventory_lat', lat, 365);
                    this.saveCookie('multi_inventory_lng', lng, 365);

                    this.getInventories(lat, lng);
                },
                (error) => {
                    this.getInventories(null, null);
                },
                {
                    enableHighAccuracy: false,
                    maximumAge: 3600000
                }
            );
        }

        this.overlay.style.display = 'block';
        this.popup.style.display = 'block';

        this.saveCookie('multi_inventory_popup', true, 365);
    }

    getInventories(lat, lng) {
        // Call AJAX to get inventories
        fetch(this.options.ajax_url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                action: 'get_inventories',
                lat: lat,
                lng: lng,
                product_id: this.productId
            })
        })
        .then(response => response.json())
        .then(response => {
            if (!response.status) {
                if (this.allLocationsContainer) {
                    this.allLocationsContainer.style.display = 'block';
                }
                
                if (this.nearestLocationLoader) {
                    this.nearestLocationLoader.style.display = 'none';
                }
                
                const locationsContainer = document.querySelector('.multi-inventory-popup-locations');
                if (locationsContainer) {
                    locationsContainer.innerHTML = 'No inventories found';
                }
                return;
            }

            const locationsContainer = document.querySelector('.multi-inventory-popup-locations');
            if (locationsContainer) {
                locationsContainer.innerHTML = response.inventories_html;
            }

            if (response.first_inventory) {
                const nearestLocationEl = document.querySelector(`.multi-inventory-popup-locations-location[data-id="${response.first_inventory}"]`);
                if (nearestLocationEl) {
                    const nearestLocationHTML = nearestLocationEl.outerHTML;
                    nearestLocationEl.style.display = 'none';
                    
                    if (this.nearestLocation) {
                        this.nearestLocation.innerHTML = nearestLocationHTML;
                    }
                }
            }

            if (this.nearestLocationError) {
                this.nearestLocationError.style.display = 'none';
            }
            
            if (this.nearestLocationLoader) {
                this.nearestLocationLoader.style.display = 'none';
            }
            
            if (this.allLocationsContainer) {
                this.allLocationsContainer.style.display = 'block';
            }
            
            if (this.deliveryLocationContainer) {
                this.deliveryLocationContainer.style.display = 'block';
                
                const locationElements = this.deliveryLocationContainer.querySelectorAll('.multi-inventory-popup-locations-location');
                locationElements.forEach(el => {
                    el.style.display = 'block';
                });
            }
        })
        .catch(error => {
            console.error('Error fetching inventories:', error);
            
            if (this.nearestLocationLoader) {
                this.nearestLocationLoader.style.display = 'none';
            }
            
            if (this.allLocationsContainer) {
                this.allLocationsContainer.style.display = 'block';
            }
        });
    }

    setupChangeInventory() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.multi-inventory-label-change')) {
                e.preventDefault();
                const inventoriesRow = document.querySelector('.multi-inventory-label-container .multi-inventory-inventories-row');
                if (inventoriesRow) {
                    inventoriesRow.style.display = inventoriesRow.style.display === 'none' ? 'block' : 'none';
                }
            }
        });
    }

    setupVariationStock() {
        const variationForm = document.querySelector('.variations_form');
        if (!variationForm) {
            return;
        }

        // Listen for variation changes
        document.addEventListener('show_variation', (e) => {
            const variation = e.detail;
            if (!variation || !variation.variation_id) {
                return;
            }

            const inventoriesContainer = document.querySelector('.multi-inventory-inventories-variable');
            if (!inventoriesContainer) {
                return;
            }

            const spinner = document.querySelector('.multi-inventory-manager-table-spinner');
            if (spinner) {
                spinner.style.display = 'block';
            }

            // Fetch variation stock info
            fetch(this.options.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    action: 'get_variation_stock',
                    variation_id: variation.variation_id
                })
            })
            .then(response => response.json())
            .then(response => {
                if (!response.status) {
                    if (inventoriesContainer) {
                        inventoriesContainer.style.display = 'none';
                    }
                    
                    if (spinner) {
                        spinner.style.display = 'none';
                    }
                    return;
                }

                // Update dropdown select if exists
                const select = document.getElementById('multi-inventory-select');
                if (select) {
                    Object.entries(response.inventories_stock).forEach(([id, stock]) => {
                        const option = select.querySelector(`option[value="${id}"]`);
                        if (!option) {
                            return;
                        }

                        const name = option.dataset.name;
                        if (this.options.productPageStockDisplay === "count") {
                            option.textContent = `${name} ${this.options.texts.leftInStock.replace('%s', stock)}`;
                        } else if (this.options.productPageStockDisplay === "inout") {
                            option.textContent = `${name} ${stock > 0 ? this.options.texts.inStock : this.options.texts.outOfStock}`;
                        } else {
                            option.textContent = name;
                        }

                        option.disabled = stock <= 0;
                    });
                } else {
                    // Update radio buttons
                    Object.entries(response.inventories_stock).forEach(([id, stock]) => {
                        const inventory = document.querySelector(`.multi-inventory-inventories-row-inventory-${id}`);
                        if (!inventory) {
                            return;
                        }

                        const stockEl = inventory.querySelector('.multi-inventory-inventories-stock');
                        if (stockEl) {
                            if (this.options.productPageStockDisplay === "count") {
                                stockEl.textContent = this.options.texts.leftInStock.replace('%s', stock);
                            } else if (this.options.productPageStockDisplay === "inout") {
                                stockEl.textContent = stock > 0 ? this.options.texts.inStock : this.options.texts.outOfStock;
                            } else {
                                stockEl.textContent = '';
                            }

                            if (stock > 0) {
                                stockEl.classList.remove('multi-inventory-inventories-stock-out-of-stock');
                                stockEl.classList.add('multi-inventory-inventories-stock-on-stock');
                            } else {
                                stockEl.classList.remove('multi-inventory-inventories-stock-on-stock');
                                stockEl.classList.add('multi-inventory-inventories-stock-out-of-stock');
                            }
                        }

                        const radioInput = inventory.querySelector('.multi-inventory-inventories-radio input');
                        if (radioInput) {
                            radioInput.disabled = stock <= 0;
                        }
                    });
                }

                // Set default selected inventory
                let existingInventory = this.readCookie('multi_inventory_inventory');
                if (!existingInventory) {
                    existingInventory = this.options.defaultInventory;
                }

                if (existingInventory) {
                    if (!select) {
                        const existingInventoryInput = document.querySelector(`.multi-inventory-inventories-row-inventory-${existingInventory} input`);
                        if (existingInventoryInput) {
                            existingInventoryInput.checked = true;
                        }
                    }

                    if (this.options.productPageDisplay === "label" && existingInventory) {
                        const labelStockEl = document.querySelector('.multi-inventory-label-current-stock');
                        if (labelStockEl) {
                            labelStockEl.textContent = response.inventories_stock[existingInventory];
                        }
                    }
                }

                // Update text display
                if (this.options.productPageDisplay === "text" || this.options.productPageDisplay === "textOnlySelected") {
                    const textEl = document.querySelector('.multi-inventory-text');
                    if (textEl) {
                        textEl.innerHTML = response.text;
                    }
                }

                inventoriesContainer.style.display = 'block';
                if (spinner) {
                    spinner.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error fetching variation stock:', error);
                if (spinner) {
                    spinner.style.display = 'none';
                }
            });
        });

        // Trigger variation change after a short delay
        setTimeout(() => {
            const variationSelects = document.querySelectorAll('.variations select');
            variationSelects.forEach(select => {
                select.dispatchEvent(new Event('change'));
            });
        }, 250);
    }

    // Utility functions
    getDistance(lat1, lon1, lat2, lon2) {
        const R = this.options.popupMiles === "1" ? 3956 : 6371; // miles or km
        const dLat = this.toRad(lat2 - lat1);
        const dLon = this.toRad(lon2 - lon1);
        const lat1Rad = this.toRad(lat1);
        const lat2Rad = this.toRad(lat2);

        const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
            Math.sin(dLon / 2) * Math.sin(dLon / 2) * Math.cos(lat1Rad) * Math.cos(lat2Rad);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
        return R * c;
    }

    toRad(value) {
        return value * Math.PI / 180;
    }

    getParameterByName(name, url = window.location.href) {
        name = name.replace(/[\[\]]/g, '\\$&');
        const regex = new RegExp(`[?&]${name}(=([^&#]*)|&|#|$)`);
        const results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, ' '));
    }

    addOrUpdateUrlParam(url, param, value) {
        const re = new RegExp(`([?&])${param}=.*?(&|$)`, 'i');
        const separator = url.indexOf('?') !== -1 ? '&' : '?';
        
        if (url.match(re)) {
            return url.replace(re, `$1${param}=${value}$2`);
        } else {
            return `${url}${separator}${param}=${value}`;
        }
    }

    saveCookie(name, value, days) {
        let expires = '';
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = `; expires=${date.toGMTString()}`;
        }

        document.cookie = `${name}=${JSON.stringify(value)}${expires}; path=/;`;
    }

    readCookie(name) {
        const nameEQ = `${name}=`;
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') {
                c = c.substring(1, c.length);
            }
            if (c.indexOf(nameEQ) === 0) {
                return JSON.parse(c.substring(nameEQ.length, c.length));
            }
        }
        return null;
    }

    deleteCookie(name) {
        document.cookie = `${name}=; expires=Thu, 01-Jan-1970 00:00:01 GMT; path=/;`;
    }
}

// Initialize on document ready
document.addEventListener('DOMContentLoaded', function() {
    window.inventoryLocations = new InventoryLocations(window.multiInventoryOptions || {});
});