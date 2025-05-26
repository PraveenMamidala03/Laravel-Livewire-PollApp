<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Poll;
use App\Models\Option;
use Illuminate\Database\Eloquent\Collection;
class CreatePoll extends Component
{

    public $title;
    public $options = ['first'];
     protected $rules = [
        'title' => 'required|min:3|max:255',
        'options' => 'required|array|min:1|max:10',
        'options.*' => 'required|min:1|max:255'
    ];

    protected $messages = [
        'options.*' => 'The option can\'t be empty.'
    ];
    public function render()
    {
        return view('livewire.create-poll');
    }
    public function addOption()
    {
        $this->options[] = '';
    }
    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options); // Re-index the array
    }
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    public function createPoll()
    {
        $this->validate();
        $poll = Poll::create([
            'title' => $this->title
            ])->options()->createMany(
                collect($this->options)
                ->map(fn ($optionName)=>['name'=>$optionName])
                ->all()
            );
        // Alternatively, you can use the following code to create options
        //  $poll = Poll::create([
        //     'title' => $this->title
        // ]);   
        // foreach ($this->options as $optionName) {
        //     $poll->options()->create(['name' => $optionName]);
        // }

        $this->reset(['title', 'options']);
        $this->emit('pollCreated');
    }
}
