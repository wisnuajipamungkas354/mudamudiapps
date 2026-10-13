<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class LogViewer extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Log Viewer';

    protected static ?string $title = 'Log Viewer';

    protected static ?string $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 99;

    protected static string $view = 'filament.pages.log-viewer';

    public static function canAccess(): bool
    {
        return auth()->check() && (auth()->user()->is_admin || auth()->user()->hasRole(['super_admin', 'Admin']));
    }
}
