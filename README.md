# invenbin

schema
![Mermaid Schema](public/images/mermaid-schema.png)
```
%%{init: {'theme': 'base', 'themeVariables': { 'primaryColor': '#4CAF50', 'secondaryColor': '#03A9F4', 'tertiaryColor': '#f0ad4e', 'primaryBorderColor': '#333', 'noteBorderColor': '#333', 'rowBorderColor': '#333', 'startArrowColor': '#333', 'endArrowColor': '#333', 'textColor': '#333', 'fontSize': 16 }}}%%
classDiagram
    class EmailsQueue {
        +id: integer
        +email: string
        +email_sent: boolean
        +text_of_email: text
        +date_sent: timestamp
        +date_queued: timestamp
        +created_at: timestamp
        +updated_at: timestamp
    }
    class ErpProductStatus {
        +id: integer
        +status: string
        +created_at: timestamp
        +updated_at: timestamp
        +updated_by: string
        has many ErpProduct
    }
    class ErpProductType {
        +id: integer
        +product_type: string
        +created_at: timestamp
        +updated_at: timestamp
        +updated_by: string
        has many ErpProduct
    }
    class ErpBillOfMaterials {
        +id: integer
        +product_id: integer
        +bom_name: string
        +date_created: datetime
        +updated_by: string
        +created_at: timestamp
        +updated_at: timestamp
        has many ErpComponent
    }
    class ErpComponent {
        +id: integer
        +item_description: string
        +adjustment_units: decimal
        +erp_bom_id: foreign key
        +created_at: timestamp
        +updated_at: timestamp
        belongs to ErpBillOfMaterials
    }
    class ErpCategory {
        +id: integer
        +category_name: string
        +image_file: string
        +parent_id: integer
        +short_description: string
        +long_description: text
        +bom_id: integer
        +created_at: timestamp
        +updated_at: timestamp
        +updated_by: string
        has many ErpProduct
    }
    class ErpProduct {
        +id: integer
        +sku: string
        +product_name: string
        +short_description: string
        +attribute_xml: text
        +stock_location: string
        +our_price: decimal
        +retail_price: decimal
        +weight: decimal
        +currency_code: string
        +unit_of_measure_id: foreign key
        +admin_comments: string
        +length: decimal
        +height: decimal
        +width: decimal
        +dimension_unit_id: foreign key
        +list_order: integer
        +rating_sum: integer
        +total_rating_votes: integer
        +default_image: string
        +owned_by: integer
        +inventory_count: integer
        +reorder_point: integer
        +product_status_id: foreign key
        +product_type_id: foreign key
        +created_at: timestamp
        +updated_at: timestamp
        +updated_by: string
        belongs to ErpUnitOfMeasure
        belongs to ErpProductStatus
        belongs to ErpProductType
        has many ErpImage
        has many ErpProductDescriptor
        has many ErpProductCategoryMap
        has many ErpProductUsageLog
        has one ErpBillOfMaterials
    }
    class ErpProductUsageLog {
        +id: integer
        +erp_product_id: foreign key
        +adjustment: integer
        +adjustment_type: string
        +reason: string
        +updated_by: foreign key
        +created_at: timestamp
        +updated_at: timestamp
        belongs to ErpProduct
        belongs to Users
    }
    class ErpImage {
        +id: integer
        +image_file: string
        +erp_product_id: foreign key
        +list_order: integer
        +caption: string
        +created_at: timestamp
        +updated_at: timestamp
        +updated_by: string
        belongs to ErpProduct
    }
    class ErpProductDescriptor {
        +id: integer
        +title: string
        +descriptor: string
        +is_bulleted_list: boolean
        +list_order: integer
        +created_at: timestamp
        +updated_at: timestamp
        +erp_product_id: foreign key
        belongs to ErpProduct
    }
    class ErpProductCategoryMap {
        +erp_product_id: foreign key
        +erp_category_id: foreign key
        +list_order: integer
        +is_featured: boolean
        +created_at: timestamp
        +updated_at: timestamp
        +updated_by: string
        belongs to ErpProduct
        belongs to ErpCategory
        key (erp_product_id, erp_category_id)
    }

    EmailsQueue "1" -- "0..1" ErpProduct : "1..*" 
    ErpProductStatus "1" -- "0..*" ErpProduct : "1..*" 
    ErpProductType "1" -- "0..*" ErpProduct : "1..*" 
    ErpUnitOfMeasure "1" -- "0..*" ErpProduct : "1..*" 
    ErpBillOfMaterials "1" -- "0..*" ErpComponent : "1..*" 
    ErpCategory "1" -- "0..*" ErpProduct : "1..*" 
    ErpProduct "1" -- "0..*" ErpImage : "1..*" 
    ErpProduct "1" -- "0..*" ErpProductDescriptor : "1..*" 
    ErpProduct "1" -- "0..*" ErpProductCategoryMap : "1..*" 
    ErpProduct "1" -- "0..*" ErpProductUsageLog : "1..*" 
    ErpProduct "1" -- "1" ErpBillOfMaterials : "1..1"


```

# Inventory Management System

This Inventory Management System is designed to provide efficient tracking and management of inventory for an ERP system. It allows users to manage products, categories, product types, product statuses, bills of material, and their corresponding items.

## Features

- **Product Management**: Create, update, delete, and view details of products.
- **Inventory Management**: Track current stock levels of products and update inventory details.
- **Usage Logging**: Log usage of products, including quantity removed, usage location, and user details.
- **Category Management**: Organize products into categories for better organization and navigation.
- **Product Type Management**: Define different types of products for classification purposes.
- **Product Status Management**: Manage the status of products, such as availability or condition.
- **User List Management**: Create and manage lists of users for various purposes.
- **User List Item Management**: Add, remove, and update items within bills of material.

## Technologies Used

- Laravel: Backend framework for building the application logic and APIs.
- MySQL: Database management system for storing inventory and related data.
- Mermaid: Used for generating diagrams to visualize the schema and relationships.

## Getting Started

1. Clone the repository.
2. Configure your environment variables in the `.env` file.
3. Run `composer install` to install PHP dependencies.
4. Run `php artisan migrate` to create the database tables.
5. Serve the application using `php artisan serve`.
6. Access the application in your web browser.

# Faxt Invenbin

A description of your package.

## Installation

To install the `faxt/invenbin` package, follow the steps below:

### Step 1: Add Repository to Composer

If your package is not hosted on Packagist, add the repository to your `composer.json` file. 

Open your project's `composer.json` file and add the following under the `repositories` section:

```json
"repositories": [
    {
        "type": "path",
        "url": "packages/faxt/invenbin"
    }
]
```
### Step 2: Require the Package

Run the following command to require the package:

``` bash

composer require faxt/invenbin:dev-main
``` 

If you want to install a specific version, replace dev-main with the desired version.

### Step 3: Publish the Configuration (Optional)

If your package includes configuration files, publish them using the following command:

```bash
php artisan vendor:publish --provider="Faxt\Invenbin\InvenbinServiceProvider" --tag="config"
```
### Step 3.5: Configure Swagger Paths  (Optional)
If you want to use Swagger, You need to add your package's source path to the Swagger configuration. Open config/l5-swagger.php and find the paths section. Add the path to your package's source directory.
Example
```
'paths' => [
        'annotations' => [
            base_path('app'),
            base_path('packages/faxt/invenbin/src'),
        ],
        'docs' => storage_path('api-docs'),
        'views' => base_path('resources/views/vendor/l5-swagger'),
    ],
    ```

### Register the admin panel

The admin panel needs registering in your app service provider before you can use it.

```php
use Faxt\Invenbin\Support\Facades\InvenbinPanel;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        InvenbinPanel::register();
    }
```
### Step 4: Run Migrations (If Applicable)

run the migrations using the following command:

```bash

php artisan migrate
```

### Usage
You can now use the faxt/invenbin package in your Laravel application. Refer to the documentation for more details on how to use the package.

### Additional Information
For more detailed information about the package and its usage, please refer to the documentation or contact the author:

Author: Bobbi Perreault
Email: bcp@faxt.com


By following these instructions, you should be able to install and use `faxt/invenbin` package in your Laravel projects. 

## Usage

- Visit `/api/documentation` to view the API documentation and interact with the endpoints using tools like Swagger or Postman.
- Use the provided API endpoints to manage products, inventory, usage logs, categories, product types, product statuses, bills of material, and bill of material items.

## Contributing

Contributions are welcome! Feel free to open issues or pull requests for any improvements or bug fixes.

## License

This project is licensed under the [MIT License](LICENSE).

