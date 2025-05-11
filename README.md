# 📚 Book Finder

Este proyecto es una aplicación en PHP orientada a objetos que permite buscar y listar libros de autores utilizando la API de Open Library. Se enfoca en el uso de **namespaces**, **autoload** y una estructura de carpetas moderna para un código limpio y mantenible.

---

## 📂 Estructura del Proyecto

```
root/
├── src/
│   ├── Models/
│   │   ├── Author.php
│   │   └── Book.php
│   └── Services/
│       └── BookFetcher.php
│       └── CsvEsporter.php
├── vendor/
│   └── (Archivos de Composer)
├── authors.csv
├── books_output.csv
├── index.php
├── composer.json
├── .env
└── README.md
```

## 📦 Requisitos

* PHP >= 8.0
* Composer
* Extensión cURL habilitada

---

## 🚀 Instalación

1. Clonar el repositorio o descarga los archivos del proyecto:

```bash
git clone https://github.com/tu-usuario/book-finder.git
cd book-finder
```

2. Instala las dependencias usando Composer:

```bash
composer install
```

3. Crea un archivo **`.env`** en el directorio raíz con las siguientes variables:

```ini
AUTHOR_LIMIT=50
TOTAL_BOOK_LIMIT=150
MAX_EXECUTION_TIME=300
MEMORY_LIMIT=512M
```

4. Crea un archivo **`authors.csv`** en el directorio raíz con los IDs de los autores (uno por línea), por ejemplo:

```
OL27695A
OL26320A
OL19767A
OL2632116A
```

---

## 📜 Uso del Proyecto

1. Corre el servidor local de PHP (puedes usar Laragon, XAMPP, MAMP o el servidor embebido de PHP):

```bash
php -S localhost:8000
```
```bash
php -S localhost:8000 -t public
```

2. Abre el navegador y visita:

```
http://localhost:8000/
```

3. El script generará una tabla HTML con hasta 50 libros por autor, mostrando solo libros con descripciones y valoraciones válidas.

---

## 🛠️ Funcionalidades

* Filtrado de libros sin descripción (en lugar de null, dejé un mensaje para los libros sin descripción pues de lo contrario devuelve pocos resultados)
* Exclusión de libros con valoración igual a 0.00 (código comentado pues de lo contrario devuelve pocos resultados)
* Limitación de 50 libros por autor
* Manejo de múltiples solicitudes en paralelo para mejorar el rendimiento
* Exportación de resultados a **`books_output.csv`**

---

## 🗂️ Organización del Código

### Namespaces y Autoload

El proyecto utiliza namespaces para organizar el código de manera clara y estructurada:

* **`App\Models`**: Incluye las clases **`Author`** y **`Book`**.
* **`App\Services`**: Incluye la clase **`BookFetcher`** para manejar las solicitudes API.

Los namespaces son una forma de organizar y agrupar el código en PHP para evitar conflictos de nombres entre clases, funciones y constantes, especialmente en proyectos grandes. Son equivalentes a los paquetes en Java o los módulos en Python.

Ventajas de usar Namespaces:

* Evitan conflictos de nombres al usar clases con nombres similares.

* Mejoran la organización del código.

* Facilitan el uso de bibliotecas externas.

* Permiten un código más limpio y estructurado.

El autoload se gestiona automáticamente mediante Composer usando el siguiente archivo **`composer.json`**:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```
El autoload es una funcionalidad que permite cargar automáticamente las clases en PHP cuando se requieren, sin tener que hacer require o include manualmente en cada archivo. Esto es especialmente útil en proyectos grandes y cuando se usan namespaces.

Cómo funciona el Autoload en Composer:
Composer utiliza PSR-4 para gestionar el autoload. Esto significa que se mapea un namespace a una carpeta específica del proyecto para que las clases se carguen automáticamente.

Por ello, hay que asegurarse de ejecutar `composer dump-autoload` cada vez que agregues nuevas clases o carpetas para actualizar el autoloader.

---

## 📝 Personalización

Puedes ajustar las ponderaciones de los resultados modificando las constantes en **`BookFetcher.php`** para priorizar diferentes criterios como año de publicación o valoración promedio.

---

## 🗑️ Limpieza

Para limpiar los datos generados, simplemente elimina el archivo **`books_output.csv`** antes de la próxima ejecución.

---

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor, crea un fork del repositorio y envía un pull request con tus mejoras.

---

## 📄 Licencia

Este proyecto está bajo la licencia MIT. Puedes usarlo libremente para proyectos personales y comerciales.

---

¡Gracias por usar Book Finder! 😊
