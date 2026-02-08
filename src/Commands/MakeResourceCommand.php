<?php

namespace Rhaima\VoltPanel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeResourceCommand extends Command
{
    protected $signature = 'voltpanel:resource {name} {--model=}';

    protected $description = 'Create a new VoltPanel resource';

    public function handle(): int
    {
        $name = $this->argument('name');
        $className = Str::studly($name);
        $model = $this->option('model') ?? Str::beforeLast($className, 'Resource');

        $stub = $this->getStub();
        $stub = str_replace('{{className}}', $className, $stub);
        $stub = str_replace('{{model}}', $model, $stub);

        $path = app_path("VoltPanel/Resources/{$className}Resource.php");

        if (file_exists($path)) {
            $this->error("Resource {$className} already exists!");

            return self::FAILURE;
        }

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $stub);

        $this->info("Resource {$className} created successfully!");

        return self::SUCCESS;
    }

    protected function getStub(): string
    {
        return <<<'PHP'
<?php

namespace App\VoltPanel\Resources;

use App\Models\{{model}};
use Rhaima\VoltPanel\Forms\Components\TextInput;
use Rhaima\VoltPanel\Forms\Form;
use Rhaima\VoltPanel\Resources\Pages\CreateRecord;
use Rhaima\VoltPanel\Resources\Pages\EditRecord;
use Rhaima\VoltPanel\Resources\Pages\ListRecords;
use Rhaima\VoltPanel\Resources\Resource;
use Rhaima\VoltPanel\Tables\Columns\TextColumn;
use Rhaima\VoltPanel\Tables\Table;

class {{className}}Resource extends Resource
{
    protected static ?string $model = {{model}}::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRecords::class,
            'create' => CreateRecord::class,
            'edit' => EditRecord::class,
        ];
    }
}
PHP;
    }
}
