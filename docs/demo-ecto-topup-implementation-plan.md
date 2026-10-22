# Demo Ecto Top-up Feature

Simulation only: no real-money payment processing.

- Reuse users.ecto as the authoritative in-game balance.
- Add top_up_requests for pending/approved/rejected demo requests.
- Add wallet_transactions as an audit ledger with a unique top_up_request_id for credits.
- Use database transactions and row locks for approval; only pending requests can be reviewed.
- Admin-only approval/rejection; users can view only their own requests.
- Test duplicate approval, authorization, invalid amounts, and rollback behavior.
- Do not deploy or run production migrations without explicit confirmation.
