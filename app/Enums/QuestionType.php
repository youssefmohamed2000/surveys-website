<?php

namespace App\Enums;


enum QuestionType: string
{
  case Text = 'text';
  case Textarea = 'textarea';
  case Select = 'select';
  case Radio = 'radio';
  case Checkbox = 'checkbox';
}
