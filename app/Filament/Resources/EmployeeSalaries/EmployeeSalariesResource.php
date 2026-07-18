<?php

namespace App\Filament\Resources\EmployeeSalaries;

use App\Filament\Resources\EmployeeSalaries\Pages\CreateEmployeeSalaries;
use App\Filament\Resources\EmployeeSalaries\Pages\EditEmployeeSalaries;
use App\Filament\Resources\EmployeeSalaries\Pages\ListEmployeeSalaries;
use App\Filament\Resources\EmployeeSalaries\Schemas\EmployeeSalariesForm;
use App\Filament\Resources\EmployeeSalaries\Tables\EmployeeSalariesTable;
use App\Models\EmployeeSalaries;
use App\Models\EmployeeSalaryHead;
// use App\Models\EmployeeSalaryRow;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployeeSalariesResource extends Resource
{
    protected static ?string $model = EmployeeSalaryHead::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'Employee Salaries';

    protected static ?string $navigationLabel = 'Gaji Karyawan';

    protected static ?string $modelLabel = 'Employee Salary';

    protected static ?string $pluralModelLabel = 'Gaji Karyawan';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return EmployeeSalariesForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeSalariesTable::configure($table);
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
            'index' => ListEmployeeSalaries::route('/'),
            'create' => CreateEmployeeSalaries::route('/create'),
            'edit' => EditEmployeeSalaries::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user.roles', 'rows.salary'])
            ->select('employee_salary_heads.*')
            ->selectSub(function ($query) {
                $query->from('employee_salary_rows')
                    ->leftJoin('salaries', 'employee_salary_rows.salary_id', '=', 'salaries.id')
                    ->selectRaw("
                        COALESCE(SUM(
                            CASE 
                                WHEN salaries.salary_type = 'harian' THEN employee_salary_rows.value * 25
                                ELSE employee_salary_rows.value
                            END
                        ), 0)
                    ")
                    ->whereColumn(
                        'employee_salary_rows.employee_salary_head_id',
                        'employee_salary_heads.id'
                    );
            }, 'total_gaji');
    }
}
