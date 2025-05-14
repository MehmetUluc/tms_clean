<?php

namespace App\Plugins\ThemeManager\View\Components;

use Illuminate\View\Component;

class ColorPreview extends Component
{
    public ?string $primaryColor;
    public ?string $secondaryColor;
    public ?string $accentColor;
    public ?string $textPrimaryColor;
    public ?string $textSecondaryColor;
    public ?string $textMutedColor;
    public ?string $backgroundColor;
    public ?string $borderColor;
    public ?string $successColor;
    public ?string $successLightColor;
    public ?string $errorColor;
    public ?string $errorLightColor;
    public ?string $warningColor;
    public ?string $warningLightColor;
    public ?string $headerBgColor;
    public ?string $headerTextColor;
    public ?string $footerBgColor;
    public ?string $footerTextColor;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $primaryColor = null,
        ?string $secondaryColor = null,
        ?string $accentColor = null,
        ?string $textPrimaryColor = null,
        ?string $textSecondaryColor = null,
        ?string $textMutedColor = null,
        ?string $backgroundColor = null,
        ?string $borderColor = null,
        ?string $successColor = null,
        ?string $successLightColor = null,
        ?string $errorColor = null,
        ?string $errorLightColor = null,
        ?string $warningColor = null,
        ?string $warningLightColor = null,
        ?string $headerBgColor = null,
        ?string $headerTextColor = null,
        ?string $footerBgColor = null,
        ?string $footerTextColor = null
    ) {
        $this->primaryColor = $primaryColor ?? '#3b82f6';
        $this->secondaryColor = $secondaryColor ?? '#10b981';
        $this->accentColor = $accentColor ?? '#f59e0b';
        $this->textPrimaryColor = $textPrimaryColor ?? '#1f2937';
        $this->textSecondaryColor = $textSecondaryColor ?? '#4b5563';
        $this->textMutedColor = $textMutedColor ?? '#9ca3af';
        $this->backgroundColor = $backgroundColor ?? '#ffffff';
        $this->borderColor = $borderColor ?? '#e5e7eb';
        $this->successColor = $successColor ?? '#059669';
        $this->successLightColor = $successLightColor ?? '#d1fae5';
        $this->errorColor = $errorColor ?? '#dc2626';
        $this->errorLightColor = $errorLightColor ?? '#fee2e2';
        $this->warningColor = $warningColor ?? '#d97706';
        $this->warningLightColor = $warningLightColor ?? '#fef3c7';
        $this->headerBgColor = $headerBgColor ?? '#1e40af';
        $this->headerTextColor = $headerTextColor ?? '#ffffff';
        $this->footerBgColor = $footerBgColor ?? '#1e293b';
        $this->footerTextColor = $footerTextColor ?? '#f3f4f6';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render()
    {
        return view('theme-manager::components.color-preview');
    }
}