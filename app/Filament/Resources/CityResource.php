<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\CityResource\RelationManagers;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';


    protected static ?string $navigationLabel = 'city'; //for change resource name in the sidebar

//CountryResource::$navigationLabel = 'country';
    protected static ?string $navigationGroup = 'System Management'; //for grouping in the sidebar

    protected static ?string $modelLabel = 'city'; //for change the title of the page and button

    protected static ?string $slug = 'city'; //for changing the url

    protected static ?int $navigationSort = 3; //for order in the sidebar


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('state_id')
                    ->relationship(name: 'state', titleAttribute: 'name')
                    ->searchable()//for search
                    ->preload()//for get all country and apply search
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        $cardFields = array();
        foreach (config('translatable.locales') as $locale => $name) {
            $translatedLabel = ucfirst(__($name));
            $cardFields[] = TextColumn::make($locale . '.name')
                ->label(__('City.' . $translatedLabel . 'Name'))->getStateUsing(
                    function ($record) use ($locale) {
                        $translation = $record->getTranslation($locale);
                        return $translation->name;
                    }
                );
        }
        $cardFields[] = Tables\Columns\TextColumn::make('state.name')
            ->searchable(isIndividual: true)// isIndividual: true for search in this column only
            ->sortable();
        $cardFields[] = Tables\Columns\TextColumn::make('name')
            ->searchable(isIndividual: true)// isIndividual: true for search in this column only                Tables\Columns\TextColumn::make('created_at')
            ->sortable();
        $cardFields [] = Tables\Columns\TextColumn::make('updated_at')
            ->dateTime()
            ->sortable()
            ->toggleable(isToggledHiddenByDefault: true);
        return $table->columns($cardFields)
        ->filters([
            //
        ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'view' => Pages\ViewCity::route('/{record}'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
