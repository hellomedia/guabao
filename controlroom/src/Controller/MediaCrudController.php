<?php

namespace Controlroom\Controller;

use App\Entity\Media;
use App\Enum\MediaType;
use App\Helper\GoogleMapsApiHelper;
use App\Helper\MediaAutoFillHelper;
use Pack\Media\Helper\ExifExtractor;
use Pack\Media\Helper\UploadHelper;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class MediaCrudController extends AbstractCrudController
{
    public function __construct(
        protected MediaAutoFillHelper $autoFillHelper,
        protected GoogleMapsApiHelper $mapsApiHelper,
        protected ExifExtractor $exifExtractor,
        protected UploadHelper $uploadHelper,
        protected UrlGeneratorInterface $urlGenerator,
    )
    {
    }

    public static function getEntityFqcn(): string
    {
        return Media::class;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(EntityFilter::new('trip'))
            ->add(EntityFilter::new('story'))
        ;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->_customFormProcessing($entityInstance);

        parent::updateEntity($entityManager, $entityInstance);
    }

    protected function _customFormProcessing(Media $media): void 
    {
        // 'Media' = entity name
        // 'imageFile' = name of field with FileType form type
        $uploadedFile = $this->getContext()->getRequest()->files->get('Media')['imageFile'] ?? null;

        if ($uploadedFile instanceof UploadedFile) {

            $media->setType(MediaType::IMAGE);

            // extract exif before upload -- exif data lost during conversion from jpeg to avif
            $exif = $this->exifExtractor->extractExifData($uploadedFile);

            // upload and convert to avif
            $this->uploadHelper->uploadImage($media, $uploadedFile, resize: $this->_shouldBeResized($media));
        }

        // Video from Vimeo
        if ($media->getVimeoId() != null) {

            $media->setType(MediaType::VIDEO);
            
            // set takenAt from hint
            if ($media->getTakenAtHint() != null) {
                $takenAt = DateTimeImmutable::createFromFormat('Ymd_His', $media->getTakenAtHint());
                $media->setTakenAt($takenAt);
                $media->setTakenAtHint(null);
            }
        }

        $this->_updateAutoFields($media, $exif ?? false);
    }

    protected function _shouldBeResized(Media $image): bool
    {
        if ($image->is360()) {
            return false;
        }

        if ($image->isPano()) {
            return false;
        }

        if ($image->isTripCover()) {
            return false;
        }

        return true;
    }

    protected function _updateAutoFields(Media $media, array|false $exif)
    {
        if ($exif) {
            $this->autoFillHelper->setTakenAt($media, $exif);
            $this->autoFillHelper->setCoordinates($media, $exif);
        }

        $this->autoFillHelper->autoAssignPlace($media);

        if ($media->getPlace() == null) {
            $suggestion = $this->autoFillHelper->suggestPlace($media);

            if ($suggestion) {
                $this->addFlash('info', $suggestion);
            }
        }

        $this->autoFillHelper->autoAssignTrip($media);
        $this->autoFillHelper->autoAssignMeal($media);
    }
}
