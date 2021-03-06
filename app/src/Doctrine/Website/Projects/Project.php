<?php

namespace Doctrine\Website\Projects;

class Project
{
    /** @var bool */
    private $active;

    /** @var bool */
    private $archived;

    /** @var string */
    private $name;

    /** @var string */
    private $shortName;

    /** @var string */
    private $slug;

    /** @var string */
    private $docsSlug;

    /** @var string */
    private $composerPackageName;

    /** @var string */
    private $repositoryName;

    /** @var bool */
    private $hasDocs = false;

    /** @var string */
    private $docsRepositoryName;

    /** @var string */
    private $docsPath;

    /** @var string */
    private $codePath;

    /** @var string */
    private $description;

    /** @var array */
    private $keywords = [];

    /** @var ProjectVersions */
    private $versions = [];

    public function __construct(array $project)
    {
        $this->active = (bool) ($project['active'] ?? true);
        $this->archived = (bool) ($project['archived'] ?? false);
        $this->name = (string) ($project['name'] ?? '');
        $this->shortName = (string) ($project['shortName'] ?? '');
        $this->slug = (string) ($project['slug'] ?? '');
        $this->docsSlug = (string) ($project['docsSlug'] ?? '');
        $this->composerPackageName = (string) ($project['composerPackageName'] ?? '');
        $this->repositoryName = (string) ($project['repositoryName'] ?? '');
        $this->hasDocs = $project['hasDocs'] ?? true;
        $this->docsRepositoryName = (string) ($project['docsRepositoryName'] ?? '');
        $this->docsPath = (string) ($project['docsPath'] ?? '');
        $this->codePath = (string) ($project['codePath'] ?? '/lib');
        $this->description = (string) ($project['description'] ?? '');
        $this->keywords = $project['keywords'] ?? [];
        $this->versions = new ProjectVersions($project['versions'] ?? []);
    }

    public function isActive() : bool
    {
        return $this->active;
    }

    public function isArchived() : bool
    {
        return $this->archived;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getShortName() : string
    {
        return $this->shortName;
    }

    public function getSlug() : string
    {
        return $this->slug;
    }

    public function getDocsSlug() : string
    {
        return $this->docsSlug;
    }

    public function getComposerPackageName() : string
    {
        return $this->composerPackageName;
    }

    public function getRepositoryName() : string
    {
        return $this->repositoryName;
    }

    public function hasDocs() : bool
    {
        return $this->hasDocs;
    }

    public function getDocsRepositoryName() : string
    {
        return $this->docsRepositoryName;
    }

    public function getDocsPath() : string
    {
        return $this->docsPath;
    }

    public function getCodePath() : string
    {
        return $this->codePath;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function getKeywords() : array
    {
        return $this->keywords;
    }

    public function getVersions() : ProjectVersions
    {
        return $this->versions;
    }

    public function getVersion(string $slug) : ProjectVersion
    {
        foreach ($this->versions as $version) {
            if ($version->getSlug() == $slug) {
                return $version;
            }
        }

        return null;
    }

    public function getCurrentVersion()
    {
        foreach ($this->versions as $version) {
            if ($version->isCurrent()) {
                return $version;
            }
        }

        return $version ?? null;
    }

    public function getProjectDocsRepositoryPath(string $projectsPath) : string
    {
        return $projectsPath.'/'.$this->getDocsRepositoryName();
    }

    public function getProjectRepositoryPath(string $projectsPath) : string
    {
        return $projectsPath.'/'.$this->getRepositoryName();
    }

    public function getAbsoluteDocsPath(string $projectsPath) : string
    {
        return $this->getProjectDocsRepositoryPath($projectsPath).$this->getDocsPath();
    }
}
