<?php
declare(strict_types=1);

namespace ExpoOne;

class Engine
{
    private Parser $parser;

    public function __construct(
        private string $templateRoot,
        private string $cacheDir
    ) {
        $this->parser = new Parser();
        $this->templateRoot = rtrim($templateRoot, '/\\');
        $this->cacheDir = rtrim($cacheDir, '/\\');

        // Make cache directory if not exist
        if (!is_dir($this->cacheDir)) {
            if (!mkdir($this->cacheDir, 0777, true)) {
                die("Error: Could not create cache directory: {$this->cacheDir}");
            }
        }
    }

    /**
     * Parses and renders an HTML template string.
     *
     * @param string $html The HTML template content.
     * @return string The rendered output.
     */
    public function parseString(string $html): string
    {
        $root = $this->parser->parse($html);
        
        $output = '';
        foreach ($root->children as $node) {
            $output .= $node->toRender();
        }
        
        return $output;
    }

    /**
     * Parses a template file and saves it as a compiled PHP file.
     *
     * @param string $templatePath The path including the source HTML file name.
     * @return string
     * @throws ParseException If parsing fails.
     */
    public function render(string $templatePath, array $data = []): string
    {
        $templateLocation = $this->templateRoot . '/' . $templatePath;

        if (!file_exists($templateLocation)) {
            throw new ParseException("Template file not found: {$templateLocation}");
        }

        $content = file_get_contents($templateLocation);
        if ($content === false) {
            throw new ParseException("Failed to read template file: {$templateLocation}");
        }

        $templateHash = md5($content);
        $cachedFile = $this->cacheDir . '/' . $templateHash . '.php';

        // 캐시 파일이 없거나 템플릿이 변경되었으면 컴파일합니다.
        if (!file_exists($cachedFile)) {
            $rendered = $this->parseString($content);
            file_put_contents($cachedFile, $rendered);
        }

        ob_start();
        // $data 변수를 템플릿 파일에서 사용할 수 있도록 include 시에 전달합니다.
        // $data_for_include = $data; // include 스코프에 $data 변수를 전달하기 위함
        include $cachedFile;
        $output = ob_get_clean();

        if(!$output) {
            throw new ParseException("Failed to read cached file: {$cachedFile}");
        }

        return $output;
    }
}

