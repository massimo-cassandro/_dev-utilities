<?php

// // da
// // https://gist.github.com/stof/c5aac17a5049dacc4ec3


// class FormRenderer 
// {
//     private $twig;

//     /**
//      * @param \Twig_Environment     $twig
//      */
//     public function __construct(\Twig_Environment $twig, FormRendererInterface $formRenderer)
//     {
//         $this->twig = $twig;
//         $this->formRenderer = $formRenderer;
//     }

//     // /**
//     //  * {@inheritDoc}
//     //  *
//     //  */
//     // public function render(Message $message)
//     // {
//     //     /** @var $template \Twig_Template */
//     //     $template = $this->twig->loadTemplate($message->getTemplate());
//     //     $context = $this->twig->mergeGlobals($message->getContext());

//     //     $subject = $this->renderBlock($template, 'subject', $context);
//     //     $textBody = $this->renderBlock($template, 'body_text', $context);
//     //     $htmlBody = $this->renderBlock($template, 'body_html', $context);

//     //     $htmlBody = $this->styleInliner->inlineStyle($htmlBody);

//     //     return array(
//     //         'subject' => $subject,
//     //         'text' => $textBody,
//     //         'html' => $htmlBody,
//     //     );
//     // }

//     public function form_row(Form $form, array $attributes)
//     {
//         /** @var $template \Twig_Template */
//         $template = $this->twig->loadTemplate($form_template);

//         $attr = $attributes;
//         $form_element = $this->renderBlock($template, 'form_row', $context);
        

//         return $form_element;
//     }

//     /**
//      * Renders a Twig block with error handling.
//      *
//      * This avoids getting some leaked buffer when an exception occurs.
//      * Twig blocks are not taking care of it as they are not meant to be rendered directly.
//      *
//      * @param \Twig_Template $template
//      * @param string         $block
//      * @param array          $context
//      *
//      * @return string
//      *
//      * @throws \Exception
//      */
//     private function renderBlock(\Twig_Template $template, $block, array $context)
//     {
//         $level = ob_get_level();
//         ob_start();
//         try {
//             $rendered = $template->renderBlock($block, $context);
//             ob_end_clean();

//             return $rendered;
//         } catch (\Exception $e) {
//             while (ob_get_level() > $level) {
//                 ob_end_clean();
//             }

//             throw $e;
//         }
//     }
// }


$formTemplateContent = 

$function = new Twig_SimpleFunction('form_row', function ($form, $attr=[]) {
    
    
});
$twig->addFunction($function);