<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\SoftDeleteable\Traits\SoftDeleteableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SchoolRepository")
 * @ORM\Table(name="app_schools")
 * @Gedmo\SoftDeleteable()
 */
class School
{
    use SoftDeleteableEntity;

    public const LEVEL_REGULAR_EDUCATION = 'regular_education';
    public const LEVEL_SPECIAL_NEEDS_EDUCATION = 'special_needs_education';

    public const TYPE_PRIMARY_EDUCATION = 'primary_education'; // Kleuter- en lager onderwijs
    public const TYPE_SECONDARY_EDUCATION = 'secondary_education'; // Secundair onderwijs
    public const TYPE_PRE_PRIMARY_EDUCATION = 'pre_primary_education'; // kleuteronderwijs
    public const TYPE_ONLY_PRIMARY_EDUCATION = 'only_primary_education'; // lager onderwijs

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $address = null;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private ?string $postalCode = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $region = null;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private ?string $city = null;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private ?string $website = null;

    /**
     * @ORM\Column(type="string", length=320, nullable=true)
     */
    private ?string $email = null;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private ?string $phoneNumber = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private ?string $institutionNumber = null;

    /**
     * @ORM\Column(type="simple_array", nullable=true)
     * @Assert\Count(min=1)
     *
     * @var list<int>
     */
    private array $establishmentNumbers = [];

    /**
     * @ORM\Column(type="string", length=120, nullable=true)
     */
    private ?string $type = null;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private string $level = self::LEVEL_REGULAR_EDUCATION;

    /**
     * @var Collection<array-key, SchoolEducation>
     *
     * @ORM\OneToMany(targetEntity="App\Entity\SchoolEducation", mappedBy="school", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private Collection $educations;

    public function __construct()
    {
        $this->educations = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->getName() ?? '';
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function getWebsiteUrl(): ?string
    {
        if (null === $this->website) {
            return null;
        }

        $website = trim($this->website);
        if ('' === $website) {
            return null;
        }

        if (preg_match('#^https?://#i', $website)) {
            return $website;
        }

        return sprintf('https://%s', ltrim($website, '/'));
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(?string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getInstitutionNumber(): ?string
    {
        return $this->institutionNumber;
    }

    public function setInstitutionNumber(?string $institutionNumber): void
    {
        $this->institutionNumber = $institutionNumber;
    }

    /**
     * @return list<int>
     */
    public function getEstablishmentNumbers(): array
    {
        return $this->establishmentNumbers;
    }

    /**
     * @param list<int> $establishmentNumbers
     */
    public function setEstablishmentNumbers(array $establishmentNumbers): void
    {
        $this->establishmentNumbers = $establishmentNumbers;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getLevel(): string
    {
        return $this->level;
    }

    public function setLevel(string $level): void
    {
        $this->level = $level;
    }

    /**
     * @return Collection<array-key, SchoolEducation>
     */
    public function getEducations(): Collection
    {
        return $this->educations;
    }

    /**
     * @param Collection<array-key, SchoolEducation> $educations
     */
    public function setEducations(Collection $educations): void
    {
        $this->educations = $educations;
    }

    public function getSchoolLevelTypes(): array
    {
        if (self::TYPE_PRE_PRIMARY_EDUCATION === $this->type) {
            return [SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION];
        }

        if (self::TYPE_ONLY_PRIMARY_EDUCATION === $this->type) {
            return [SchoolLevel::TYPE_PRIMARY_EDUCATION];
        }

        if (self::TYPE_PRIMARY_EDUCATION === $this->type) {
            return [
                SchoolLevel::TYPE_PRE_PRIMARY_EDUCATION,
                SchoolLevel::TYPE_PRIMARY_EDUCATION,
            ];
        }

        if (self::TYPE_SECONDARY_EDUCATION === $this->type) {
            return [SchoolLevel::TYPE_SECONDARY_EDUCATION];
        }

        return [];
    }

    public function getSchoolLevelLevels(): array
    {
        if (self::LEVEL_REGULAR_EDUCATION === $this->level) {
            return [SchoolLevel::LEVEL_REGULAR_EDUCATION, SchoolLevel::LEVEL_RECEPTION_EDUCATION];
        }

        if (self::LEVEL_SPECIAL_NEEDS_EDUCATION === $this->level) {
            return [SchoolLevel::LEVEL_SPECIAL_NEEDS_EDUCATION];
        }

        return [];
    }

    public function removeEducation(SchoolEducation $education): void
    {
        if ($this->educations->contains($education)) {
            $this->educations->removeElement($education);
            $education->setSchool(null);
        }
    }

    public function addEducation(SchoolEducation $education): void
    {
        if (!$this->educations->contains($education)) {
            $this->educations->add($education);
            $education->setSchool($this);
        }
    }

    public function isPrimaryEducation(): bool
    {
        return self::TYPE_PRIMARY_EDUCATION === $this->type;
    }

    public function isSecondaryEducation(): bool
    {
        return self::TYPE_SECONDARY_EDUCATION === $this->type;
    }

    public function isRegularEducation(): bool
    {
        return self::LEVEL_REGULAR_EDUCATION === $this->level;
    }

    public function isSpecialNeedsEducation(): bool
    {
        return self::LEVEL_SPECIAL_NEEDS_EDUCATION === $this->level;
    }

    /**
     * @return Collection<array-key, SchoolEducation>
     */
    public function getEducationsForLevelAndYear(SchoolLevel $level, SchoolYear $schoolYear): Collection
    {
        $criteria = new Criteria();
        $criteria->andWhere(new Comparison('level', '=', $level));
        $criteria->andWhere(new Comparison('year', '=', $schoolYear));

        $educations = $this->educations;
        if ($educations instanceof Selectable) {
            return $educations->matching($criteria);
        }

        throw new \RuntimeException('Could not get educations for level');
    }

    /**
     * @return Collection<array-key, SchoolEducation>
     */
    public function getEducationsForYear(SchoolYear $schoolYear): Collection
    {
        $criteria = new Criteria();
        $criteria->andWhere(new Comparison('year', '=', $schoolYear));

        $educations = $this->educations;
        if ($educations instanceof Selectable) {
            return $educations->matching($criteria);
        }

        throw new \RuntimeException('Could not get educations for year');
    }
}
