# Guía: Importar Recursos desde Excel

## Pasos

1. En la sección de Recursos, presiona el botón **"Importar Excel"**
2. Se abrirá un modal donde debes:
   - **Seleccionar el tipo de recurso**: Material, Herramienta o Mano de Obra
   - **Seleccionar el archivo Excel**: Elige un archivo .xlsx o .xls

## Formato del Archivo Excel

El archivo debe tener 3 columnas:

| Columna A | Columna B | Columna C |
|-----------|-----------|-----------|
| **Nombre** | **Unidad** | **Precio USD** |
| Cable Unipolar 2.5mm | m | 0.80 |
| Llave Térmica 16A | u | 5.50 |
| Mano de obra | h | 25.00 |

### Ejemplo Correcto:
```
Cable Unipolar 2.5mm    m       0.80
Cable Unipolar 1.5mm    m       0.60
Llave Térmica 16A       u       5.50
Tomacorriente Doble     u       3.20
```

## Notas Importantes

- **Primera fila**: La primera fila del archivo será ignorada (generalmente es el encabezado)
- **Columna A (Nombre)**: Requerida. No puede estar vacía
- **Columna B (Unidad)**: Opcional. Si no se especifica, se usa "unidad"
- **Columna C (Precio)**: Requerida. Debe ser un número válido
- **Todos los recursos importados** tendrán el mismo tipo seleccionado en el paso 1
- **Historial de precios**: Se registra automáticamente "Importado desde Excel"

## Resultado

Después de importar:
- ✓ Verás un resumen de cuántos recursos se importaron correctamente
- ✓ Si hay errores, se mostrarán las filas problemáticas
- ✓ Los recursos aparecerán inmediatamente en la lista
- ✓ El precio de cada recurso queda registrado en el historial de precios

## Errores Comunes

- **"Fila X: Faltan datos válidos"**: Falta el nombre o el precio no es un número
- **"Fila X: [mensaje error]"**: Intenta con otro nombre o verifica que el precio sea correcto
