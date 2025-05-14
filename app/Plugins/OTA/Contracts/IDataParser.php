<?php

namespace App\Plugins\OTA\Contracts;

interface IDataParser
{
    /**
     * Parse content string and extract all paths
     *
     * @param string $content
     * @return array
     */
    public function parse(string $content): array;
    
    /**
     * Format parsed paths for UI display
     *
     * @param array $paths
     * @return array
     */
    public function formatPathsForUI(array $paths): array;
    
    /**
     * Generate mapping template from parsed paths
     *
     * @param array $paths
     * @return array
     */
    public function generateMappingTemplate(array $paths): array;
    
    /**
     * Find example data from content
     *
     * @param string $content
     * @return array
     */
    public function getExampleData(string $content): array;
    
    /**
     * Check if the parser can handle this content
     *
     * @param string $content
     * @return bool
     */
    public function canParse(string $content): bool;
    
    /**
     * Get the format type this parser handles
     *
     * @return string
     */
    public function getFormatType(): string;
    
    /**
     * Transform data using provided mapping
     *
     * @param string $content Source content to transform
     * @param array $mapping Mapping rules
     * @param string $template Optional template for export transformations
     * @return mixed Transformed data
     */
    public function transform(string $content, array $mapping, ?string $template = null): mixed;
}