<?php declare(strict_types=1);

namespace Web200\Seo\Setup\Patch\Schema;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;

class AddOpenGraphImageUrlToCmsPage implements SchemaPatchInterface
{
    private ModuleDataSetupInterface $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    public function apply(): self
    {
        $this->moduleDataSetup->startSetup();

        $connection = $this->moduleDataSetup->getConnection();
        $tableName = $this->moduleDataSetup->getTable('cms_page');
        $columnName = 'open_graph_image_url';

        if (!$connection->tableColumnExists($tableName, $columnName)) {
            $connection->addColumn(
                $tableName,
                $columnName,
                [
                    'type' => Table::TYPE_TEXT,
                    'length' => 255,
                    'nullable' => true,
                    'comment' => 'Open Graph Image Url',
                ]
            );
        }

        $this->moduleDataSetup->endSetup();

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}