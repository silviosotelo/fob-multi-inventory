<?php

namespace FriendsOfBotble\MultiInventory\Forms;

use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\EmailFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\EmailField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\FormAbstract;
use Botble\Ecommerce\Facades\EcommerceHelper;
use FriendsOfBotble\MultiInventory\Models\Inventory;
use FriendsOfBotble\MultiInventory\Http\Requests\InventoryRequest;

class InventoryForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Inventory::class)
            ->setValidatorClass(InventoryRequest::class)
            ->withCustomFields()
            ->add(
                'name',
                TextField::class,
                NameFieldOption::make()
                    ->label(trans('core/base::forms.name'))
                    ->required()
            )
            ->add(
                'description',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('core/base::forms.description'))
                    ->rows(4)
                    ->placeholder(trans('core/base::forms.description_placeholder'))
                    ->maxLength(400)
            )
            ->add(
                'address',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.address'))
                    ->placeholder(trans('plugins/multi-inventory::multi-inventory.address_placeholder'))
                    ->maxLength(255)
            )
            ->add(
                'city',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.city'))
                    ->placeholder(trans('plugins/multi-inventory::multi-inventory.city_placeholder'))
                    ->maxLength(255)
            )
            ->add(
                'state',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.state'))
                    ->placeholder(trans('plugins/multi-inventory::multi-inventory.state_placeholder'))
                    ->maxLength(255)
            )
            ->add(
                'country',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.country'))
                    ->choices(EcommerceHelper::getAvailableCountries())
                    ->searchable()
            )
            ->add(
                'zip_code',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.zip_code'))
                    ->placeholder(trans('plugins/multi-inventory::multi-inventory.zip_code_placeholder'))
                    ->maxLength(20)
            )
            ->add(
                'email',
                EmailField::class,
                EmailFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.email'))
                    ->placeholder(trans('plugins/multi-inventory::multi-inventory.email_placeholder'))
                    ->maxLength(60)
            )
            ->add(
                'phone',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.phone'))
                    ->placeholder(trans('plugins/multi-inventory::multi-inventory.phone_placeholder'))
                    ->maxLength(20)
            )
            ->add(
                'latitude',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.latitude'))
                    ->placeholder('40.7128')
                    ->maxLength(20)
            )
            ->add(
                'longitude',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.longitude'))
                    ->placeholder('-74.0060')
                    ->maxLength(20)
            )
            ->add(
                'is_default',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.is_default'))
                    ->defaultValue(false)
            )
            ->add(
                'is_frontend',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.is_frontend'))
                    ->defaultValue(true)
                    ->helperText(trans('plugins/multi-inventory::multi-inventory.is_frontend_help'))
            )
            ->add(
                'is_backend',
                OnOffField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.is_backend'))
                    ->defaultValue(true)
                    ->helperText(trans('plugins/multi-inventory::multi-inventory.is_backend_help'))
            )
            ->add(
                'delivery_time',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.delivery_time'))
                    ->placeholder(trans('plugins/multi-inventory::multi-inventory.delivery_time_placeholder'))
                    ->maxLength(255)
            )
            ->add(
                'order_priority',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/multi-inventory::multi-inventory.order_priority'))
                    ->placeholder('0')
                    ->defaultValue(0)
            )
            ->add(
                'status',
                SelectField::class,
                StatusFieldOption::make()
            )
            ->setBreakFieldPoint('status');
    }
}