import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import { Head, useForm } from '@inertiajs/react';
import { route } from 'ziggy-js';

export default function Create({ board }) {
    const { data, setData, post, processing, errors } = useForm({
        title: '',
        type: 'flyer',
        content: '',
        file: null,
    });

    const submit = (e) => {
        e.preventDefault();

        post(route('submissions.store', board.id), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    Submit to {board.name}
                </h2>
            }
        >
            <Head title="Create Submission" />

            <div className="py-12">
                <div className="mx-auto max-w-3xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white p-6 shadow-sm sm:rounded-lg">
                        <form onSubmit={submit} className="space-y-6">
                            <div>
                                <InputLabel htmlFor="title" value="Title" />
                                <TextInput
                                    id="title"
                                    className="mt-1 block w-full"
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                />
                                <InputError message={errors.title} className="mt-2" />
                            </div>

                            <div>
                                <InputLabel htmlFor="type" value="Type" />
                                <select
                                    id="type"
                                    value={data.type}
                                    onChange={(e) => {
                                        const nextType = e.target.value;
                                        setData('type', nextType);

                                        if (nextType === 'flyer') {
                                            setData('file', null);
                                        }

                                        if (nextType === 'poster') {
                                            setData('content', '');
                                        }
                                    }}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="flyer">Flyer</option>
                                    <option value="poster">Poster</option>
                                </select>
                                <InputError message={errors.type} className="mt-2" />
                            </div>

                            {data.type === 'flyer' && (
                                <div>
                                    <InputLabel htmlFor="content" value="Flyer Content" />
                                    <textarea
                                        id="content"
                                        rows="8"
                                        value={data.content}
                                        onChange={(e) => setData('content', e.target.value)}
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                    <InputError message={errors.content} className="mt-2" />
                                </div>
                            )}

                            {data.type === 'poster' && (
                                <div>
                                    <InputLabel htmlFor="file" value="Poster Image" />
                                    <input
                                        id="file"
                                        type="file"
                                        accept="image/*"
                                        onChange={(e) => setData('file', e.target.files[0])}
                                        className="mt-1 block w-full rounded-md border border-gray-300 p-2"
                                    />
                                    <p className="mt-2 text-xs text-gray-500">
                                        Accepted types: jpg, jpeg, png, webp. Max size: 5MB.
                                    </p>
                                    <InputError message={errors.file} className="mt-2" />
                                </div>
                            )}

                            <PrimaryButton disabled={processing}>
                                Send for Approval
                            </PrimaryButton>
                        </form>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
