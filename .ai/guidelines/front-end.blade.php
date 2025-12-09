<front-end-guidelines>
Whenever you are creating a new page on the front-end and it has a crud functionality.
You should create only index.tsx page and in the partials folder for the feature you should create a form that is both for edit and create functionality.
The form should be shown as a dialog.
Avoid creating interfaces in the component. Use laravel-data for creating a type definition on the backend and use it in the component. using `php artisan typescript:transform` command.

    <code-example language="tsx" description="Example of a index page">
        import TableAction from '@/components/table-action';
        import { Button } from '@/components/ui/button';
        import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
        import { Empty, EmptyContent, EmptyDescription, EmptyHeader, EmptyMedia, EmptyTitle } from '@/components/ui/empty';
        import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
        import AppLayout from '@/layouts/app-layout';
        import StockMarketForm from '@/pages/stock-market/partials/stock-market-form';
        import { destroy } from '@/wayfinder/routes/user-stock-market';
        import { Head, router } from '@inertiajs/react';
        import { TrendingUp } from 'lucide-react';
        import { useState } from 'react';
        import UserStockMarketData = App.Data.App.StockMarket.UserStockMarketData;

        export default function Index({ columns, rows }: { columns: string[]; rows?: UserStockMarketData[] }) {
        const [open, setOpen] = useState(false);
        const [openEdit, setOpenEdit] = useState(false);
        const [selectedRow, setSelectedRow] = useState<UserStockMarketData | null>(null);

            const handleDelete = (row: UserStockMarketData) => {
            router.delete(destroy(row.id).url);
            };
            const handleEditClick = (row: UserStockMarketData) => {
            setSelectedRow(row);
            setOpenEdit(true);
            };

            return (
            <AppLayout>
                <Head title="Stock market portfolio" />

                <div className={`flex h-full max-w-full flex-1 flex-col gap-4 rounded-xl p-4`}>
                    {!rows || rows.length === 0 ? (
                    <Empty>
                        <EmptyHeader>
                            <EmptyMedia variant="icon">
                                <TrendingUp />
                            </EmptyMedia>
                            <EmptyTitle>No stock holdings yet</EmptyTitle>
                            <EmptyDescription>
                                Track your stock portfolio by adding ticker symbols (e.g., AAPL, TSLA) along with the number of shares you own. We'll
                                automatically fetch current prices and calculate your holdings.
                            </EmptyDescription>
                        </EmptyHeader>
                        <EmptyContent>
                            <Button onClick={() => setOpen(true)}>Add stock ticker</Button>
                        </EmptyContent>
                    </Empty>
                    ) : (
                    <>
                    <div className={`flex items-center space-x-2 self-end`}>
                        <Button onClick={() => setOpen(true)}>Add stock ticker</Button>
                    </div>
                    <Table>
                        <TableHeader>
                            <TableRow>
                                {columns.map((column: string) => (
                                <TableHead key={column}>{column}</TableHead>
                                ))}
                                <TableHead className="relative w-0">
                                    <span className="sr-only">Actions</span>
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            {rows?.map((row: UserStockMarketData) => (
                            <TableRow key={row.id}>
                                <TableCell>{row.ticker}</TableCell>
                                <TableCell>{row.amount}</TableCell>
                                <TableCell>{row.balance}</TableCell>
                                <TableCell>
                                    <TableAction row={row} destroy={() => handleDelete(row)} handleEditClick={() => handleEditClick(row)} />
                                </TableCell>
                            </TableRow>
                            ))}
                        </TableBody>
                    </Table>
                </>
                )}
                </div>

                <Dialog open={open} onOpenChange={() => setOpen(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Add stock ticker</DialogTitle>
                    </DialogHeader>
                    <StockMarketForm closeModal={() => setOpen(false)} />
                </DialogContent>
                </Dialog>
                <Dialog open={openEdit} onOpenChange={() => setOpenEdit(false)}>
                <DialogContent>
                    <DialogHeader>
                        <DialogTitle>Update stock ticker</DialogTitle>
                    </DialogHeader>
                    {selectedRow && <StockMarketForm stockMarket={selectedRow} closeModal={() => setOpenEdit(false)} />}
                </DialogContent>
                </Dialog>
            </AppLayout>
            );
            }

    </code-example>

    <code-example language="tsx" description="Example of a form component">
        import { Button } from '@/components/ui/button';
        import { FormMessage } from '@/components/ui/form';
        import { Input } from '@/components/ui/input';
        import { Label } from '@/components/ui/label';
        import { store, update } from '@/wayfinder/actions/App/Http/Controllers/App/UserStockMarketController';
        import { useForm } from '@inertiajs/react';
        import { Loader2 } from 'lucide-react';
        import { FormEventHandler } from 'react';
        import StockMarketData = App.Data.App.StockMarket.StockMarketData;
        import UserStockMarketData = App.Data.App.StockMarket.UserStockMarketData;

        export default function StockMarketForm({ stockMarket, closeModal }: { stockMarket?: UserStockMarketData; closeModal: () => void }) {
        const { data, setData, errors, submit, processing, reset } = useForm<StockMarketData>({
            ticker: stockMarket?.ticker || '',
            amount: stockMarket?.amount || 0,
            });

            const handleSubmit: FormEventHandler = (e) => {
            e.preventDefault();

            if (stockMarket !== undefined) {
            submit(update(stockMarket.id), {
            onSuccess: () => {
            setTimeout(() => {
            reset();
            closeModal();
            }, 100);
            },
            only: ['rows', 'flash'],
            });
            } else {
            submit(store(), {
            onSuccess: () => {
            setTimeout(() => {
            reset();
            closeModal();
            }, 100);
            },
            only: ['rows', 'flash'],
            });
            }
            };

            return (
            <form onSubmit={handleSubmit} className="mt-6 space-y-6">
                <div className="grid gap-2">
                    <Label htmlFor={'ticker'}>Ticker</Label>
                    <Input
                        id={'ticker'}
                        name={'ticker'}
                        value={data.ticker}
                        onChange={(e) => {
                    setData('ticker', e.target.value);
                    }}
                    autoFocus={true}
                    />
                    {errors.ticker !== undefined && <FormMessage>{errors.ticker}</FormMessage>}
                </div>
                <div className="grid gap-2">
                    <Label htmlFor={'amount'}>Amount</Label>
                    <Input
                        id={'amount'}
                        name={'amount'}
                        value={data.amount}
                        type={'number'}
                        step={'0.001'}
                        onChange={(e) => {
                    setData('amount', Number.parseFloat(e.target.value));
                    }}
                    />
                    {errors.amount !== undefined && <FormMessage>{errors.amount}</FormMessage>}
                </div>

                <div className="flex items-center gap-4">
                    <Button type={'submit'} color={'sky'} className={'mt-5'} disabled={processing}>
                        {processing ? <Loader2 className="mr-2 h-4 w-4 animate-spin" /> : <></>}
                    {stockMarket !== undefined ? 'Update' : 'Create'}
                    </Button>
                </div>
            </form>
            );
            }
    </code-example>
</front-end-guidelines>
