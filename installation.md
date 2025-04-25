# Multi Inventory Plugin para Botble CMS

Este plugin permite gestionar múltiples inventarios y almacenes para tu tienda de comercio electrónico con Botble CMS.

## Instalación

### Requisitos
- Botble CMS con el plugin Ecommerce instalado
- PHP 7.4 o superior
- Composer

### Pasos de instalación

1. Descarga el plugin y colócalo en la carpeta `platform/plugins/multi-inventory`

2. Activa el plugin desde el panel de administración o mediante el comando:
   ```bash
   php artisan cms:plugin:activate multi-inventory
   ```

3. Publica los assets del plugin:
   ```bash
   php artisan vendor:publish --tag=multi-inventory-assets
   ```

4. Aplica las migraciones para crear las tablas necesarias:
   ```bash
   php artisan migrate
   ```

5. Limpia la caché:
   ```bash
   php artisan optimize:clear
   ```

## Configuración

1. Accede a la configuración del plugin desde el menú **Ecommerce > Multi Inventario > Configuración**

2. Configura las siguientes opciones:
   - Click & Collect: Habilitar/deshabilitar la funcionalidad de recoger en tienda
   - Inventario para entrega: Seleccionar el inventario predeterminado para entregas
   - Tipo de visualización: Elegir entre radios, dropdown, etiquetas o oculto
   - Flujo de pedidos: Seleccionar cómo se asignan los pedidos a los inventarios
   - Visualización de stock: Configurar cómo se muestra el stock al cliente

## Uso

### Gestión de inventarios

1. Crea inventarios en **Ecommerce > Multi Inventario > Inventarios**

2. Para cada inventario puedes configurar:
   - Nombre y descripción
   - Dirección y datos de contacto
   - Ubicación en mapa (latitud/longitud)
   - Tiempo de entrega estimado
   - Visibilidad en frontend y backend
   - Prioridad de orden

### Asignación de stock a productos

1. Edita un producto y encuentra la sección "Gestión de Inventario"

2. Asigna el stock y precios específicos para cada inventario

3. El stock total del producto se actualizará automáticamente sumando el stock de todos los inventarios

### Frontend

Los clientes podrán:
- Seleccionar el inventario al comprar productos
- Ver el stock disponible en cada ubicación
- Ver precios específicos por inventario (si están configurados)
- Ver los tiempos de entrega estimados

### Importación/Exportación

El plugin incluye funcionalidad para:
- Exportar todos los datos de inventario a Excel
- Importar stock y precios desde un archivo Excel
- Descargar una plantilla de muestra para la importación

## Personalización

### Sobreescribir vistas

Puedes sobreescribir las vistas del plugin copiándolas a la carpeta de tu tema:

```
resources/views/vendor/plugins/multi-inventory/
```

### Estilos y JavaScript

Personaliza el aspecto modificando los archivos CSS/JS:

```
public/vendor/core/plugins/multi-inventory/css/
public/vendor/core/plugins/multi-inventory/js/
```

## Funciones disponibles

El plugin extiende los modelos de Producto con el trait `HasMultiInventory`, que proporciona los siguientes métodos:

- `getStockByInventory($inventoryId)`: Obtiene el stock específico de un inventario
- `getPriceByInventory($inventoryId)`: Obtiene el precio específico de un inventario
- `updateInventoryStock($inventoryId, $quantity, $operator = '-')`: Actualiza el stock de un inventario

## Problemas conocidos y soluciones

### El stock total no se actualiza

Si encuentras problemas con la actualización del stock total, ejecuta:

```bash
php artisan multi-inventory:sync-stock
```

### La visualización del inventario no aparece

Asegúrate de que:
1. El plugin está activado
2. El tipo de visualización no está configurado como "Oculto"
3. Has configurado al menos un inventario como "Visible en frontend"
4. El producto tiene la gestión de stock habilitada

## Soporte

Para problemas o preguntas, por favor contacta al equipo de Friends of Botble o abre un issue en el repositorio del plugin.