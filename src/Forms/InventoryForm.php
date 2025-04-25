<?php

namespace FriendsOfBotble\MultiInventory\Forms;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Enums\BaseStatusEnum;
use FriendsOfBotble\MultiInventory\Models\Inventory;

class InventoryForm extends FormAbstract
{
    protected $template = 'core/base::forms.form-tabs';

    public function buildForm(): void
    {
        $this
            ->setupModel(new Inventory)
            ->withCustomFields()
            ->add('name', 'text', [
                'label' => trans('core/base::forms.name'),
                'label_attr' => ['class' => 'control-label required'],
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('description', 'textarea', [
                'label' => trans('core/base::forms.description'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                    'data-counter' => 400,
                ],
            ])
            ->add('address', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.address'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/multi-inventory::multi-inventory.address_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('city', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.city'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/multi-inventory::multi-inventory.city_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('state', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.state'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/multi-inventory::multi-inventory.state_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('country', 'customSelect', [
                'label' => trans('plugins/multi-inventory::multi-inventory.country'),
                'label_attr' => ['class' => 'control-label'],
                'choices' => get_countries_list(),
            ])
            ->add('zip_code', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.zip_code'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/multi-inventory::multi-inventory.zip_code_placeholder'),
                    'data-counter' => 20,
                ],
            ])
            ->add('email', 'email', [
                'label' => trans('plugins/multi-inventory::multi-inventory.email'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/multi-inventory::multi-inventory.email_placeholder'),
                    'data-counter' => 60,
                ],
            ])
            ->add('phone', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.phone'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/multi-inventory::multi-inventory.phone_placeholder'),
                    'data-counter' => 20,
                ],
            ])
            ->add('latitude', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.latitude'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => '40.7128',
                    'data-counter' => 20,
                ],
            ])
            ->add('longitude', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.longitude'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => '-74.0060',
                    'data-counter' => 20,
                ],
            ])
            ->add('is_default', 'onOff', [
                'label' => trans('plugins/multi-inventory::multi-inventory.is_default'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => false,
            ])
            ->add('is_frontend', 'onOff', [
                'label' => trans('plugins/multi-inventory::multi-inventory.is_frontend'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => true,
                'help_block' => [
                    'text' => trans('plugins/multi-inventory::multi-inventory.is_frontend_help'),
                ],
            ])
            ->add('is_backend', 'onOff', [
                'label' => trans('plugins/multi-inventory::multi-inventory.is_backend'),
                'label_attr' => ['class' => 'control-label'],
                'default_value' => true,
                'help_block' => [
                    'text' => trans('plugins/multi-inventory::multi-inventory.is_backend_help'),
                ],
            ])
            ->add('delivery_time', 'text', [
                'label' => trans('plugins/multi-inventory::multi-inventory.delivery_time'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => trans('plugins/multi-inventory::multi-inventory.delivery_time_placeholder'),
                    'data-counter' => 255,
                ],
            ])
            ->add('order_priority', 'number', [
                'label' => trans('plugins/multi-inventory::multi-inventory.order_priority'),
                'label_attr' => ['class' => 'control-label'],
                'attr' => [
                    'placeholder' => '0',
                ],
                'default_value' => 0,
            ])
            ->add('status', 'customSelect', [
                'label' => trans('core/base::tables.status'),
                'label_attr' => ['class' => 'control-label required'],
                'choices' => BaseStatusEnum::labels(),
            ])
            ->setBreakFieldPoint('status');
    }
}