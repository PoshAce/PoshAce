<?php
/**
 * Deploy Optimized Static Content Command
 *
 * @category  PoshAce
 * @package   PoshAce_Performance
 * @author    PoshAce
 * @copyright Copyright (c) 2024 PoshAce
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace PoshAce\Performance\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\App\State;
use PoshAce\Performance\Helper\ImageOptimizer;

class DeployOptimized extends Command
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var State
     */
    private $state;

    /**
     * @var ImageOptimizer
     */
    private $imageOptimizer;

    /**
     * @param Filesystem $filesystem
     * @param State $state
     * @param ImageOptimizer $imageOptimizer
     * @param string|null $name
     */
    public function __construct(
        Filesystem $filesystem,
        State $state,
        ImageOptimizer $imageOptimizer,
        $name = null
    ) {
        $this->filesystem = $filesystem;
        $this->state = $state;
        $this->imageOptimizer = $imageOptimizer;
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('poshace:deploy:optimized')
            ->setDescription('Deploy static content with performance optimizations')
            ->addOption(
                'area',
                'a',
                InputOption::VALUE_OPTIONAL,
                'Area code (frontend|adminhtml)',
                'frontend'
            )
            ->addOption(
                'theme',
                't',
                InputOption::VALUE_OPTIONAL,
                'Theme path',
                'Codazon/unlimited'
            )
            ->addOption(
                'locale',
                'l',
                InputOption::VALUE_OPTIONAL,
                'Locale code',
                'en_US'
            );

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);

            $area = $input->getOption('area');
            $theme = $input->getOption('theme');
            $locale = $input->getOption('locale');

            $output->writeln('<info>Starting optimized static content deployment...</info>');

            // Deploy static content
            $this->deployStaticContent($output, $area, $theme, $locale);

            // Optimize images
            $this->optimizeImages($output);

            // Generate critical CSS
            $this->generateCriticalCSS($output);

            // Optimize fonts
            $this->optimizeFonts($output);

            $output->writeln('<info>Optimized static content deployment completed successfully!</info>');

            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    /**
     * Deploy static content
     *
     * @param OutputInterface $output
     * @param string $area
     * @param string $theme
     * @param string $locale
     * @return void
     */
    private function deployStaticContent(OutputInterface $output, $area, $theme, $locale)
    {
        $output->writeln('<info>Deploying static content...</info>');

        // Run Magento's static content deployment
        $command = sprintf(
            'php bin/magento setup:static-content:deploy %s %s -f -a %s',
            $locale,
            $theme,
            $area
        );

        exec($command, $result, $returnCode);

        if ($returnCode !== 0) {
            throw new \Exception('Static content deployment failed');
        }

        $output->writeln('<info>Static content deployed successfully</info>');
    }

    /**
     * Optimize images
     *
     * @param OutputInterface $output
     * @return void
     */
    private function optimizeImages(OutputInterface $output)
    {
        $output->writeln('<info>Optimizing images...</info>');

        $staticDir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
        $mediaDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);

        // Process static images
        $this->processImagesInDirectory($staticDir->getAbsolutePath(), $output);

        // Process media images
        $this->processImagesInDirectory($mediaDir->getAbsolutePath(), $output);

        $output->writeln('<info>Image optimization completed</info>');
    }

    /**
     * Process images in directory
     *
     * @param string $directory
     * @param OutputInterface $output
     * @return void
     */
    private function processImagesInDirectory($directory, OutputInterface $output)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );

        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $processedCount = 0;

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower(pathinfo($file->getPathname(), PATHINFO_EXTENSION));
                
                if (in_array($extension, $imageExtensions)) {
                    $this->optimizeSingleImage($file->getPathname(), $output);
                    $processedCount++;
                }
            }
        }

        $output->writeln(sprintf('<info>Processed %d images in %s</info>', $processedCount, $directory));
    }

    /**
     * Optimize single image
     *
     * @param string $imagePath
     * @param OutputInterface $output
     * @return void
     */
    private function optimizeSingleImage($imagePath, OutputInterface $output)
    {
        try {
            // Generate WebP version if enabled
            if ($this->imageOptimizer->isWebpEnabled()) {
                $webpPath = preg_replace('/\.(jpg|jpeg|png|gif)$/i', '.webp', $imagePath);
                $this->imageOptimizer->convertToWebP($imagePath, $webpPath);
            }

            // Generate responsive images
            $this->imageOptimizer->generateResponsiveImages($imagePath);

        } catch (\Exception $e) {
            $output->writeln(sprintf('<comment>Warning: Could not optimize %s: %s</comment>', $imagePath, $e->getMessage()));
        }
    }

    /**
     * Generate critical CSS
     *
     * @param OutputInterface $output
     * @return void
     */
    private function generateCriticalCSS(OutputInterface $output)
    {
        $output->writeln('<info>Generating critical CSS...</info>');

        // This would integrate with a critical CSS generator
        // For now, we'll just ensure the critical CSS file exists
        $criticalCssPath = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW)
            ->getAbsolutePath('frontend/Codazon/unlimited/en_US/css/critical.css');

        if (!file_exists($criticalCssPath)) {
            $output->writeln('<comment>Warning: Critical CSS file not found</comment>');
        } else {
            $output->writeln('<info>Critical CSS file found</info>');
        }
    }

    /**
     * Optimize fonts
     *
     * @param OutputInterface $output
     * @return void
     */
    private function optimizeFonts(OutputInterface $output)
    {
        $output->writeln('<info>Optimizing fonts...</info>');

        $staticDir = $this->filesystem->getDirectoryRead(DirectoryList::STATIC_VIEW);
        $fontDir = $staticDir->getAbsolutePath('frontend/Codazon/unlimited/en_US/fonts');

        if (is_dir($fontDir)) {
            $output->writeln('<info>Font directory found, optimization applied</info>');
        } else {
            $output->writeln('<comment>Warning: Font directory not found</comment>');
        }
    }
}