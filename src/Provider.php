<?php

    $this->loadTranslationsFrom(__DIR__.'/Lang', 'Plugins/Cms/Faq');
    $this->loadViewsFrom(__DIR__.'/Views', 'Plugins/Cms/Faq');
    
    if (sc_config('Faq')) {
        view()->share('modelFaqCategory', (new \App\Plugins\Cms\Faq\Models\FaqCategory));
        view()->share('modelFaqContent', (new App\Plugins\Cms\Faq\Models\FaqContent));
    }
