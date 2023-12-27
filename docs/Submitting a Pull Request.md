# Submitting a Pull Request

> [!NOTE]
> Before submitting a pull request, make sure you've completed all the setup steps in [Local Development - Setup](Local%20Development%20-%20Setup.md).

Bowpress uses the ["Github flow"](https://docs.github.com/en/get-started/quickstart/github-flow) workflow for managing contributions from multiple people. Notably, this means that if you want to contribute code, you won't push directly to the `master` branch. Instead, you'll create a branch, commit your changes to that branch, submit a pull request, and get your code reviewed. Once your code is reviewed, you'll be able to merge your pull request, and your commits will be collapsed into a single commit on the master branch.

Submitting and merging a pull request serves two purposes: first, it encapsulates all your work into a single change and puts that change onto the shared code repository. Second, it gives other folks an opportunity to review the code you've written to make sure it'll do what's expected.

You can follow Github's ["Creating a pull request"](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request) instructions to submit the pull request.

In the pull request description, you should describe:

1. What you changed
2. Why you made this change
3. How you tested that the change worked

If you've changed something visual, you should include a screenshot of what the page looks like now.

## Pull request reviews

You should get your code reviewed by someone else by [requesting a review](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/requesting-a-pull-request-review). If you don't have someone else in mind (either a current student or a recent alum), you can always tag @jameslittle230 for a review.

Once the reviewer is finished, they will assign the PR back to you. If they requested changes, you should make those changes in new commits and re-request a review. If they approved your pull request, you should [merge the pull request](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/incorporating-changes-from-a-pull-request/merging-a-pull-request).