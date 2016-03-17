# AlpixelElasticaQuerySorterBundle

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/1f9c3f6d-db92-41e0-b310-206206c8a96e/mini.png)](https://insight.sensiolabs.com/projects/1f9c3f6d-db92-41e0-b310-206206c8a96e)
[![Build Status](https://travis-ci.org/alpixel/AlpixelElasticaQuerySorterBundle.svg?branch=master)](https://travis-ci.org/alpixel/AlpixelElasticaQuerySorterBundle)
[![StyleCI](https://styleci.io/repos/54101565/shield)](https://styleci.io/repos/54101565)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/alpixel/AlpixelElasticaQuerySorterBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/alpixel/AlpixelElasticaQuerySorterBundle/?branch=master)

## Installation

Install the bundle from composer 

`$ composer require alpixel/elastica-query-sorter-bundle`

Enable the bundle in your AppKernel.php
```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new Alpixel\Bundle\ElasticaQuerySorterBundle\AlpixelElasticaQuerySorterBundle(),
        ]
    }
}
```

### Configuration

Add minimal configuration in your config.yml

```yaml
alpixel_elastica_query_sorter:
    views:
```

#### Configuration reference

```yaml
alpixel_elastica_query_sorter:
    views:
        clear_sort: # Path to your twig file, link to the current route (path(app.request.attributes.get('_route'), {'clear_sort' : true}))
        sort_link: # Path to your twig file, check the "Resources/views/blocks/sort_link.html.twig" for more informations
    item_per_page: 10 # Number of item per page, default 25
```

### Usage

Full functional usage exemple :

First in your controller, like usualy create or use existing action of your controller.

```php
<?php

namespace AppBundle\Controller;

use AppBundle\Form\MyForm;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;

class ResearchController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function shopAction(Request $request)
    {
        $form = $this->createform(MyForm::class);
        $form->handleRequest($request);

        $data               = $form->getData();
        $repository         = $this->get('fos_elastica.manager')->getRepository('AppBundle:MyEntity');
        $query              = $repository->queryCustom($data);

        // You need to pass the repository and the query from elastica (\Elastica\Query)
        $results           = $this->get('alpixel.services.elastica_query_sorter')
            ->sort($repository, $query);

        return $this->render('page/home.html.twig', [
            'results'  => $results,
            'form'     => $form->createView(),
        ]);
    }
}
```

And in your view.

```twig
{% extends 'layout/base.html.twig' %}

{% block page %}
    {{ form_start(form) }}
        {{ form_widget(form) }}
        <input type="submit" value="Rechecher">
    {{ form_end(form) }}
    <table>
        <thead>
            <tr>
                // You must define your mapping in your elastica configuration
                // The first parameter is the label to display on your page
                // The second parameter is the mapping to you attribute, you want to sort 
                <th>{{elastica_sort('Name', 'name')}}</th>
                // If the attribute is an object, you just need apply the good mapping to the attribute
                <th>{{elastica_sort('Serial Number', 'product.serial_number')}}</th>
                <th>{{elastica_sort('Date', 'date_updated')}}</th>
            </tr>
        </thead>
        <tbody>
            {% for result in results %}
                <tr>
                    <td>{{ result.name }}</td>
                    <td>{{ result.product.serialNumber }}</td>
                    <td>{{ result.dateUpdated|date }}</td>
                </tr>
            {% endfor %}
        </tbody>
    </table>

    // The pagerfanta is used for pagination, check the documentation of this bundle
    {{ pagerfanta(results, 'twitter_bootstrap3_translated') }}

{% endblock %}
```
