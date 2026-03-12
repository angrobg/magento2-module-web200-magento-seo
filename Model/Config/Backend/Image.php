<?php declare(strict_types=1);

namespace Web200\Seo\Model\Config\Backend;

class Image extends \Magento\Config\Model\Config\Backend\Image
{
    /**
     * The tail part of directory path for uploading
     */
    public const UPLOAD_DIR = 'seomarkup'; // Folder save image

    /**
     * Return path to directory for upload file
     *
     * @throw \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir(): string
    {
        return $this->_mediaDirectory->getAbsolutePath($this->_appendScopeInfo(self::UPLOAD_DIR));
    }

    /**
     * Makes a decision about whether to add info about the scope.
     */
    protected function _addWhetherScopeInfo(): bool
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     * @return string[]
     */
    protected function _getAllowedExtensions(): array
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }
}
