<?php



/**
 * TestUser
 */
class TestUser
{
    /**
     * @var integer $id
     */
    private $id;

    /**
     * @var string $name
     */
    private $name;

    /**
     * Get id
     *
     * @return integer $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }
    /**
     * @prePersist
     */
    public function doStuffOnPrePersist()
    {
        // Add your code here
    }    /**
     * @prePersist
     */
    public function doOtherStuffOnPrePersistToo()
    {
        // Add your code here
    }    /**
     * @postPersist
     */
    public function doStuffOnPostPersist()
    {
        // Add your code here
    }






}