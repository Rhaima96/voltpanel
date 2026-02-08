# VoltPanel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/rhaima/voltpanel.svg?style=flat-square)](https://packagist.org/packages/rhaima/voltpanel)
[![Total Downloads](https://img.shields.io/packagist/dt/rhaima/voltpanel.svg?style=flat-square)](https://packagist.org/packages/rhaima/voltpanel)
[![License](https://img.shields.io/packagist/l/rhaima/voltpanel.svg?style=flat-square)](https://packagist.org/packages/rhaima/voltpanel)

A powerful admin panel builder for Laravel using the **VILT stack** (Vue, Inertia.js, Laravel, Tailwind CSS). Inspired by [Filament PHP](https://filamentphp.com), VoltPanel brings a similar developer experience to the VILT ecosystem.

## Features

- **Resource Builder** — Define models, forms, and tables in a single PHP class with zero boilerplate CRUD
- **Rich Form Components** — TextInput, Select, RichEditor (Tiptap), DatePicker, ColorPicker, FileUpload, Toggle, Checkbox, Radio, Textarea, and more
- **Powerful Tables** — Sortable, searchable, and filterable columns with TextColumn, BadgeColumn, BooleanColumn, DateColumn, ImageColumn, IconColumn
- **Table Filters** — SelectFilter, TernaryFilter with saved filter presets
- **Actions & Bulk Actions** — Built-in Delete, Export (CSV/Excel/PDF), and Import actions with custom action support
- **Dashboard Widgets** — StatsOverview, Chart (Chart.js), AdvancedChart, TimeSeries, StatsChart, and ActivityLog widgets with customizable layouts
- **Role-Based Authorization** — Built-in roles and permissions system with super admin support
- **Activity Logging** — Automatic tracking of create, update, and delete operations
- **Import & Export** — CSV, Excel (XLSX), and PDF export via OpenSpout and DomPDF; CSV/Excel import with chunked processing
- **Multi-Tenancy** — Data isolation by tenant with subdomain/domain identification support
- **Media Library** — File uploads and media management with multiple disk support
- **Global Search** — Search across all resources with customizable keybindings
- **Theming** — Customizable colors, dark mode toggle, and CSS variable-based theming (supports Tailwind v3 & v4)
- **Multi-Panel Support** — Register multiple admin panels with independent configurations
- **Comments** — Threaded comments with mentions support on any resource
- **Tags & Favorites** — Tagging system and user favorites for resources
- **Webhooks** — Event-driven webhook dispatching
- **Plugin System** — Extend VoltPanel with custom plugins
- **Localization** — Multi-language support (English, French, Spanish, German, Arabic)
- **Settings Management** — Key-value system settings with caching
- **Scheduled Exports** — Automate recurring data exports

## Requirements

- PHP 8.2+
- Laravel 11 or 12
- Node.js 18+
- Composer 2.x

## Installation

### 1. Install via Composer

```bash
composer require rhaima/voltpanel
```

### 2. Run the install command

```bash
php artisan voltpanel:install
```

This will publish the config file, migrations, Vue components, and CSS assets (auto-detects Tailwind v3 or v4).

### 3. Run migrations

```bash
php artisan migrate
```

### 4. Install frontend dependencies and build

```bash
npm install
npm run dev
```

### 5. Create your first panel

```bash
php artisan voltpanel:panel Admin
```

### 6. Register the panel

In your `app/Providers/AppServiceProvider.php`:

```php
use App\Panels\AdminPanel;
use Rhaima\VoltPanel\Facades\VoltPanel;

public function boot(): void
{
    VoltPanel::register(new AdminPanel());
}
```

### 7. Add the HasRoles trait to your User model

```php
use Rhaima\VoltPanel\Authorization\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

Visit `http://yourapp.com/admin` to access the panel.

## Quick Example

Generate a resource:

```bash
php artisan voltpanel:resource Post
```

Define your resource in a single class:

```php
use Rhaima\VoltPanel\Resources\Resource;
use Rhaima\VoltPanel\Forms\Form;
use Rhaima\VoltPanel\Tables\Table;
use Rhaima\VoltPanel\Forms\Components\TextInput;
use Rhaima\VoltPanel\Forms\Components\RichEditor;
use Rhaima\VoltPanel\Forms\Components\Select;
use Rhaima\VoltPanel\Tables\Columns\TextColumn;
use Rhaima\VoltPanel\Tables\Columns\BadgeColumn;
use Rhaima\VoltPanel\Tables\Columns\DateColumn;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('title')->required(),
            RichEditor::make('content'),
            Select::make('status')->options([
                'draft' => 'Draft',
                'published' => 'Published',
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('title')->sortable()->searchable(),
            BadgeColumn::make('status'),
            DateColumn::make('created_at'),
        ]);
    }
}
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --tag=voltpanel-config
```

See `config/voltpanel.php` for all available options including path, middleware, branding, theme, authorization, activity log, multi-tenancy, and more.

## Documentation

Full documentation is available at [https://rhaima96.github.io/voltpanel-docs](https://rhaima96.github.io/voltpanel-docs).

## Tech Stack

| Layer | Technology | Purpose |
|-------|-----------|---------|
| Backend | Laravel 11/12 | API, routing, ORM, authentication |
| Frontend | Vue 3 | Reactive UI components |
| Bridge | Inertia.js | SPA without building an API |
| Styling | Tailwind CSS v3/v4 | Utility-first CSS |
| Charts | Chart.js | Dashboard visualizations |
| Rich Editor | Tiptap | WYSIWYG content editing |
| Export | OpenSpout, DomPDF | CSV, Excel, PDF generation |

## Testing

```bash
composer test
```

## Changelog

Please see the [releases](https://github.com/rhaima96/voltpanel/releases) page for more information on what has changed.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](https://github.com/rhaima96/voltpanel/blob/main/.github/CONTRIBUTING.md) for details.

## Security

If you discover a security vulnerability, please send an email to mohamed.rhaima96@gmail.com. All security vulnerabilities will be promptly addressed.

## Credits

- [Mohamed Touhami Rhaima](https://github.com/rhaima96)
- Inspired by [Filament PHP](https://filamentphp.com)
- [All Contributors](https://github.com/rhaima96/voltpanel/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
