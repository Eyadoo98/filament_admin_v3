<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group'; //for change icon in the sidebar

    protected static ?string $navigationLabel = 'employee'; //for change resource name in the sidebar

    protected static ?string $modelLabel = 'employee'; //for change the title of the page and button

    protected static ?string $slug = 'employee'; //for changing the url

    protected static ?string $navigationGroup = 'Employee Management'; //for grouping in the sidebar

//    protected static ?string $recordTitleAttribute = 'first_name';//for add global search

    public static function getNavigationBadge(): ?string //for add count badge in the sidebar
    {
        return static::getModel()::query()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null //for change color badge in the sidebar
    {
        return 'Green';
    }

    public static function form(Form $form): Form
    {
//        select depend on other select by eyadoo
        return $form
            ->schema([
                Forms\Components\Section::make('Relation')
                    ->description('This is the user relation')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->relationship(name: 'country', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->live()// This allows the form to rerender when the value of the field changes.
                            ->afterStateUpdated(function (Forms\Set $set) {//when change country_id set state_id and city_id to null
                                $set('state_id', null);
                                $set('city_id', null);
                            })
                            ->required(),
                        Forms\Components\Select::make('state_id')->label('State')
                            ->options(//for get options based on country_id
                                fn(Forms\Get $get): Collection => State::query()
                                    ->where('country_id', $get('country_id'))
                                    ->get()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(fn(Forms\Set $set) => $set('city_id', null))//when change state_id set city_id to null
                            ->required(),

                        Forms\Components\Select::make('city_id')->label('City')
                            ->options(
                                fn(Forms\Get $get): Collection => City::query()
                                    ->where('state_id', $get('state_id'))
                                    ->get()
                                    ->pluck('name', 'id')
                            )
                            ->searchable()
                            ->preload()
                            ->live()
                            ->required(),
                        Forms\Components\Select::make('department_id')
                            ->relationship(name: 'department', titleAttribute: 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ]),


                Forms\Components\Section::make('User Name')
                    ->description('This is the user name section')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('middle_name')
                            ->required()
                            ->maxLength(255),
                    ])->columns(3),

                Forms\Components\Section::make('Addresses')
                    ->description('This is the user addresses')
                    ->schema([
                        Forms\Components\TextInput::make('address')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('zip_code')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date_of_birth')
                            ->native(false)//for change the view of date picker to native
                            ->required(),
                        Forms\Components\DatePicker::make('date_hired')
                            ->required(),
                    ])->columns(2),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('Department')
                    ->relationship('department', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filet by Department ')
                    ->indicator('Department'),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators['from'] = 'Created from ' . Carbon::parse($data['from'])->toFormattedDateString();
                        }

                        if ($data['until'] ?? null) {
                            $indicators['until'] = 'Created until ' . Carbon::parse($data['until'])->toFormattedDateString();
                        }

                        return $indicators;
                    })->columnSpan(2)->columns(2),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->successNotificationTitle('Employee Deleted Successfully'),//for custom deleted message

//                you can do that
//                Tables\Actions\DeleteAction::make()
//                    ->successNotification(
//                      Notification::make()
//                          ->success()
//                          ->title('Employee Deleted Successfully')
//                          ->body('The employee has been Deleted.'),
//                    )

//            or if you want to remove the notification
//                Tables\Actions\DeleteAction::make()
//                    ->successNotification(null)


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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
