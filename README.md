# Form Annotation Bundle

Build Symfony forms with annotation

## Requirements

> Important! PHP 7.4 is required for this bundle, because in this bundle we use typed properties feature!

## Installation

```
composer require eightmarq/form-annotation-bundle
```

## Usage

1. Annotate your entity/model
    
    ```php
    <?php
    
    declare(strict_types=1);
    
    namespace App\Model;
    
    use DateTime;
    use EightMarq\FormAnnotationBundle\Annotation as FormAnnotations;
    
    /**
     * @FormAnnotations\FormType(
     *     submit="form.submit"
     * )
     */
    class TestModel
    {
        /**
         * @var string|null
         *
         * @FormAnnotations\AddField(name="name")
         */
        protected ?string $name = null;
    
        /**
         * @var string|null
         *
         * @FormAnnotations\AddField(
         *     name="description",
         *     type="Symfony\Component\Form\Extension\Core\Type\TextareaType",
         *     options={"required": false}
         * )
         */
        protected ?string $description = null;
    
        /**
         * @FormAnnotations\AddField(
         *     name="createdAt",
         *     type="Symfony\Component\Form\Extension\Core\Type\DateTimeType"
         * )
         */
        protected ?DateTime $createdAt = null;
        
        [...]
    }
    ```

2. Create form in your controller
    
    ```php
    <?php
   
    namespace App\Controller;
    
    use App\Model\TestModel;
    use EightMarq\FormAnnotationBundle\Annotation as FormAnnotations;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
    use Symfony\Component\Form\FormInterface;
    use Symfony\Component\HttpFoundation\Request;
    use Symfony\Component\HttpFoundation\Response;
    use Symfony\Component\Routing\Annotation\Route;
    
    class TestController extends AbstractController
    {
       /**
         * @Route("/test/form", methods={"GET", "POST"})
         *
         * @FormAnnotations\CreateForm(name="TestModelForm", dataClass=TestModel::class)
         *
         * @param FormInterface $testModelForm
         *
         * @return Response
         */
        public function testFormAction(Request $request, FormInterface $testModelForm): Response
        {
            $testModelForm->handleRequest($request);
    
            if($testModelForm->isSubmitted() && $testModelForm->isValid()){
                // Handle form data
            }
    
            return $this->render('controller/test/test.html.twig', ['form' => $testModelForm->createView()]);
        }
    }
    ```

## Tests

// TODO make tests

## Development

We would like to create and maintain a great libraries for Symfony developers, 
so please help us to improve this library.

Do you have any suggestion for new features or do you find any issue?
Tell us in Github issues.